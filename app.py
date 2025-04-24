from flask import Flask, render_template, request, redirect, url_for, flash
from flask_login import LoginManager, UserMixin, login_user, login_required, logout_user, current_user
from google.oauth2.credentials import Credentials
from google_auth_oauthlib.flow import InstalledAppFlow
from googleapiclient.discovery import build
from googleapiclient.http import MediaFileUpload
import os
from werkzeug.utils import secure_filename
import uuid

# Cấu hình ứng dụng Flask
app = Flask(__name__)
app.config['SECRET_KEY'] = 'your_secret_key'
app.config['UPLOAD_FOLDER'] = './uploads'

# Cấu hình Google Drive API
SCOPES = ['https://www.googleapis.com/auth/drive.file']
creds = None
if os.path.exists('token.json'):
    creds = Credentials.from_authorized_user_file('token.json', SCOPES)

# Khởi tạo Flask-Login
login_manager = LoginManager()
login_manager.init_app(app)
login_manager.login_view = 'login'

# Tạo đối tượng User
class User(UserMixin):
    def __init__(self, id):
        self.id = id

# Tạo hàm xác thực người dùng
@login_manager.user_loader
def load_user(user_id):
    return User(user_id)

# Hàm xác thực Google Drive API
def authenticate_google_drive():
    if not creds or not creds.valid:
        if creds and creds.expired and creds.refresh_token:
            creds.refresh(Request())
        else:
            flow = InstalledAppFlow.from_client_secrets_file(
                'credentials.json', SCOPES)
            creds = flow.run_local_server(port=0)

        with open('token.json', 'w') as token:
            token.write(creds.to_json())
    service = build('drive', 'v3', credentials=creds)
    return service

# Hàm upload file lên Google Drive
def upload_file_to_google_drive(file):
    service = authenticate_google_drive()
    file_metadata = {'name': file.filename}
    media = MediaFileUpload(file, mimetype=file.content_type)

    uploaded_file = service.files().create(body=file_metadata, media_body=media, fields='id').execute()
    return uploaded_file

# Route trang chủ
@app.route('/')
@login_required
def home():
    return render_template('index.html')

# Route trang upload file
@app.route('/upload', methods=["GET", "POST"])
@login_required
def upload():
    if request.method == "POST":
        file = request.files['file']
        if file:
            filename = secure_filename(file.filename)
            filepath = os.path.join(app.config['UPLOAD_FOLDER'], filename)
            file.save(filepath)
            uploaded_file = upload_file_to_google_drive(filepath)
            flash('File uploaded successfully!', 'success')
            return redirect(url_for('home'))
        else:
            flash('No file selected!', 'danger')
    return render_template('upload.html')

# Route tải file
@app.route('/download/<file_id>')
@login_required
def download(file_id):
    # Tìm và lấy thông tin file từ Google Drive bằng file_id
    # Đây là ví dụ đơn giản, bạn cần làm việc với API Google Drive để lấy tệp.
    return render_template('download.html', file_id=file_id)

# Route đăng nhập
@app.route('/login', methods=["GET", "POST"])
def login():
    if request.method == "POST":
        username = request.form['username']
        password = request.form['password']
        if username == 'admin' and password == 'password':  # Example login
            user = User(id=uuid.uuid4().hex)
            login_user(user)
            flash('Login successful!', 'success')
            return redirect(url_for('home'))
        else:
            flash('Invalid credentials!', 'danger')
    return render_template('login.html')

# Route đăng ký
@app.route('/register', methods=["GET", "POST"])
def register():
    return render_template('register.html')

# Route đăng xuất
@app.route('/logout')
@login_required
def logout():
    logout_user()
    flash('You have been logged out.', 'info')
    return redirect(url_for('login'))

# Khởi động ứng dụng
if __name__ == '__main__':
    app.run(debug=True)
