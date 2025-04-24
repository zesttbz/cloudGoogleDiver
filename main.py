from flask import Flask
from flask_sqlalchemy import SQLAlchemy
from flask import session
from app.routes import app as app_routes

app = Flask(__name__)
app.secret_key = "super-secret"
app.config['SQLALCHEMY_DATABASE_URI'] = 'sqlite:///db.sqlite3'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

db = SQLAlchemy(app)

from app.models import User, File

app.register_blueprint(app_routes)

if __name__ == "__main__":
    db.create_all()
    app.run(debug=True)
