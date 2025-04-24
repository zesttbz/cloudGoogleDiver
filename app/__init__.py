from flask import Flask
from flask_sqlalchemy import SQLAlchemy

db = SQLAlchemy()

def create_app():
    app = Flask(__name__)
    app.config["SQLALCHEMY_DATABASE_URI"] = "sqlite:///db.sqlite3"
    app.secret_key = "supersecret"
    db.init_app(app)

    from .routes import app_routes
    from .models import User, File

    app.register_blueprint(app_routes)

    with app.app_context():
        db.create_all()

    return app  # BẮT BUỘC PHẢI return app
