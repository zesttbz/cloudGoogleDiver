from flask import Blueprint, render_template, request, session, redirect, url_for, flash
from werkzeug.security import generate_password_hash, check_password_hash
from .models import User, File
from . import db

import os
import uuid
from google.oauth2 import service_account
from googleapiclient.discovery import build
from werkzeug.utils import secure_filename
from urllib.parse import urlparse, parse_qs


app_routes = Blueprint('app_routes', __name__)


# Route xử lý tải file từ Google Drive
def extract_file_id(drive_link):
    # Hỗ trợ link dạng: https://drive.google.com/file/d/FILE_ID/view...
    # hoặc https://drive.google.com/open?id=FILE_ID
    parsed = urlparse(drive_link)
    if 'id' in parse_qs(parsed.query):
        return parse_qs(parsed.query)['id'][0]
    elif '/d/' in parsed.path:
        return parsed.path.split('/d/')[1].split('/')[0]
    else:
        return None

@app_routes.route('/copy', methods=['POST'])
def copy_file():
    if 'user_id' not in session:
        flash("Bạn cần đăng nhập.", "warning")
        return redirect(url_for('app_routes.index'))

    drive_link = request.form.get("drive_link")
    file_id = extract_file_id(drive_link)
    if not file_id:
        flash("Không thể nhận diện link Google Drive.", "danger")
        return redirect(url_for('app_routes.dashboard'))

    try:
        drive_service = get_drive_service()
        # Lấy thông tin file gốc
        file_metadata = drive_service.files().get(fileId=file_id).execute()
        # Copy file
        copied_file = drive_service.files().copy(
            fileId=file_id,
            body={"name": f"{file_metadata['name']} - copy"}
        ).execute()
        
        # Lưu vào DB
        new_file = File(
            name=copied_file['name'],
            drive_id=copied_file['id'],
            user_id=session['user_id']
        )
        db.session.add(new_file)
        db.session.commit()

        flash(f"Đã copy file: {copied_file['name']}", "success")
    except Exception as e:
        flash(f"Lỗi khi copy: {e}", "danger")

    return redirect(url_for('app_routes.dashboard'))


# Hàm khởi tạo service Google Drive
def get_drive_service():
    creds = service_account.Credentials.from_service_account_file(
        os.environ.get("GOOGLE_APPLICATION_CREDENTIALS"),
        scopes=['https://www.googleapis.com/auth/drive']
    )
    return build('drive', 'v3', credentials=creds)

@app_routes.route('/delete/<int:file_id>', methods=['POST'])
def delete_file(file_id):
    if 'user_id' not in session:
        flash("Bạn cần đăng nhập để thực hiện thao tác này.", "warning")
        return redirect(url_for('app_routes.index'))

    file = File.query.get_or_404(file_id)

    # Đảm bảo chỉ chủ sở hữu được xoá
    if file.user_id != session['user_id']:
        flash("Bạn không có quyền xoá file này.", "danger")
        return redirect(url_for('app_routes.dashboard'))

    # Xoá trên Google Drive
    try:
        drive_service = get_drive_service()
        drive_service.files().delete(fileId=file.drive_id).execute()
    except Exception as e:
        flash(f"Lỗi khi xoá trên Google Drive: {e}", "danger")

    # Xoá trong database
    db.session.delete(file)
    db.session.commit()
    flash("Đã xoá file thành công.", "success")
    return redirect(url_for('app_routes.dashboard'))


# Trang chủ
@app_routes.route('/')
def index():
    return render_template('index.html')


# Route xử lý đăng kí
@app_routes.route('/register', methods=['POST'])
def register():
    username = request.form.get('username')
    password = request.form.get('password')

    if not username or not password:
        flash('Vui lòng nhập đầy đủ thông tin.', 'danger')
        return redirect(url_for('app_routes.index'))

    existing_user = User.query.filter_by(username=username).first()
    if existing_user:
        flash('Tên người dùng đã tồn tại. Vui lòng chọn tên khác.', 'warning')
        return redirect(url_for('app_routes.index'))

    generate_password_hash(password, method='pbkdf2:sha256')
    new_user = User(username=username, password=hashed_pw)
    db.session.add(new_user)
    db.session.commit()

    flash('Đăng ký thành công! Bạn có thể đăng nhập ngay.', 'success')
    return redirect(url_for('app_routes.index'))



# Route xử lý đăng nhập
@app_routes.route('/login', methods=['POST'])
def login():
    username = request.form.get('username')
    password = request.form.get('password')

    user = User.query.filter_by(username=username).first()
    if not user or not check_password_hash(user.password, password):
        flash('Tên đăng nhập hoặc mật khẩu không đúng', 'danger')
        return redirect(url_for('app_routes.index'))

    session['user_id'] = user.id
    flash('Đăng nhập thành công!', 'success')
    return redirect(url_for('app_routes.index'))


# Route xử lý upload file lên Google Drive
@app_routes.route('/upload', methods=['POST'])
def upload():
    if 'user_id' not in session:
        flash('Bạn cần đăng nhập để upload file.', 'warning')
        return redirect(url_for('app_routes.index'))

    file = request.files['file']
    if not file:
        flash('Không có file nào được chọn.', 'danger')
        return redirect(url_for('app_routes.index'))

    filename = secure_filename(file.filename)
    filepath = os.path.join('/tmp', filename)
    file.save(filepath)

    # Xác thực Google Drive API
    creds = service_account.Credentials.from_service_account_file(
        os.environ.get("GOOGLE_APPLICATION_CREDENTIALS"),
        scopes=["https://www.googleapis.com/auth/drive"]
    )

    drive_service = build('drive', 'v3', credentials=creds)

    # Upload file
    file_metadata = {'name': filename}
    media = MediaFileUpload(filepath, resumable=True)
    uploaded_file = drive_service.files().create(
        body=file_metadata,
        media_body=media,
        fields='id'
    ).execute()

    file_id = uploaded_file.get('id')
    user_id = session['user_id']

    # Lưu vào database
    new_file = File(name=filename, drive_id=file_id, user_id=user_id)
    db.session.add(new_file)
    db.session.commit()

    flash(f'File "{filename}" đã được tải lên thành công!', 'success')
    return redirect(url_for('app_routes.index'))


# Route dashboard
@app_routes.route('/dashboard')
def dashboard():
    if 'user_id' not in session:
        flash("Bạn cần đăng nhập để truy cập trang này.", "warning")
        return redirect(url_for('app_routes.index'))

    user_id = session['user_id']
    user = User.query.get(user_id)
    files = File.query.filter_by(user_id=user_id).all()
    return render_template('dashboard.html', user=user, files=files)


# Route xử lý đăng xuất
@app_routes.route('/logout')
def logout():
    session.clear()
    flash("Bạn đã đăng xuất thành công.", "info")
    return redirect(url_for('app_routes.index'))
