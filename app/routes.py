from flask import Blueprint, render_template, request, session, redirect
from werkzeug.security import generate_password_hash, check_password_hash
from .models import User, File
from . import db

app_routes = Blueprint('app_routes', __name__)

@app_routes.route('/')
def index():
    return render_template('login.html')