from flask import Flask
from flask_sqlalchemy import SQLAlchemy

db = SQLAlchemy()

def create_app():
    app = Flask(__name__)
    
    # 🔐 Thiết lập khóa bảo mật để Flask có thể dùng session
    app.secret_key = "supersecret"
    
    # 🗂 Cấu hình database
    app.config["SQLALCHEMY_DATABASE_URI"] = "sqlite:///db.sqlite3"
    
    db.init_app(app)

    from .routes import app_routes
    from .models import User, File

    app.register_blueprint(app_routes)

    with app.app_context():
        db.create_all()

    return app

    