from flask import Blueprint, request, jsonify, redirect
from app.drive import upload_file, copy_file

api = Blueprint('api', __name__)

@api.route('/upload', methods=['POST'])
def upload():
    f = request.files['file']
    file_path = f"./temp/{f.filename}"
    f.save(file_path)
    file_id = upload_file(file_path, f.filename, f.mimetype)
    return jsonify({'file_id': file_id})

@api.route('/download/<file_id>')
def download(file_id):
    return redirect(f"https://drive.google.com/uc?id={file_id}&export=download")

@api.route('/copy', methods=['POST'])
def copy():
    data = request.json
    copied_id = copy_file(data['file_id'], data.get('new_name', 'Copied File'))
    return jsonify({'copied_id': copied_id})
