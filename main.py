from flask import Flask
from app import db
from app.routes import app as app_routes

app = Flask(__name__)
app.secret_key = "super-secret"
app.config['SQLALCHEMY_DATABASE_URI'] = 'sqlite:///db.sqlite3'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

db.init_app(app)

with app.app_context():
    from app.models import User, File
    db.create_all()

app.register_blueprint(app_routes)

if __name__ == "__main__":
    app.run(debug=True)
