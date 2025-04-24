from flask import Blueprint, render_template, redirect, request, session, url_for, flash
from werkzeug.security import generate_password_hash, check_password_hash
from app.models import User, File
from app.drive import upload_file
from main import db

app = Blueprint('app_routes', __name__)

def get_current_user():
    user_id = session.get('user_id')
    if user_id:
        return User.query.get(user_id)
    return None

@app.route('/')
def home():
    return redirect('/dashboard')

@app.route('/register', methods=['GET', 'POST'])
def register():
    if request.method == 'POST':
        email = request.form['email']
        password = request.form['password']
        if User.query.filter_by(email=email).first():
            flash('Email already exists')
            return redirect('/register')
        user = User(email=email, password=generate_password_hash(password))
        db.session.add(user)
        db.session.commit()
        session['user_id'] = user.id
        return redirect('/dashboard')
    return render_template('register.html')

@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        email = request.form['email']
        password = request.form['password']
        user = User.query.filter_by(email=email).first()
        if user and check_password_hash(user.password, password):
            session['user_id'] = user.id
            return redirect('/dashboard')
        flash('Invalid credentials')
    return render_template('login.html')

@app.route('/logout')
def logout():
    session.pop('user_id', None)
    return redirect('/login')

@app.route('/dashboard')
def dashboard():
    user = get_current_user()
    if not user:
        return redirect('/login')
    files = File.query.filter_by(user_id=user.id).all()
    return render_template('dashboard.html', user=user, files=files)

@app.route('/upload', methods=['GET', 'POST'])
def upload():
    user = get_current_user()
    if not user:
        return redirect('/login')
    if request.method == 'POST':
        f = request.files['file']
        path = f"./temp/{f.filename}"
        f.save(path)
        file_id = upload_file(path, f.filename, f.mimetype)
        db.session.add(File(filename=f.filename, drive_id=file_id, user_id=user.id))
        db.session.commit()
        return redirect('/dashboard')
    return render_template('upload.html', user=user)

@app.route('/download/<drive_id>')
def download(drive_id):
    # Tùy bạn cấu hình Google Drive sharing link (tạm dùng direct link)
    return redirect(f"https://drive.google.com/uc?id={drive_id}&export=download")
