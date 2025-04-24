from flask import Flask
from flask_sqlalchemy import SQLAlchemy

db = SQLAlchemy()

def create_app():
    app = Flask(__name__)
    app.secret_key = 'super-secret'
    app.config['SQLALCHEMY_DATABASE_URI'] = 'sqlite:///db.sqlite3'
    app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

    db.init_app(app)

    # Import các route & model ở đây (sau khi app khởi tạo)
    from .routes import app_routes
    from .models import User, File
    app.register_blueprint(app_routes)

    with app.app_context():
        db.create_all()

    return app
