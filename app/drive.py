from googleapiclient.discovery import build
from googleapiclient.http import MediaFileUpload
from google.oauth2.credentials import Credentials

def get_drive_service():
    creds = Credentials.from_authorized_user_file('token.json')
    return build('drive', 'v3', credentials=creds)

def upload_file(filepath, filename, mimetype):
    drive = get_drive_service()
    file_metadata = {'name': filename}
    media = MediaFileUpload(filepath, mimetype=mimetype)
    file = drive.files().create(body=file_metadata, media_body=media, fields='id').execute()
    return file.get('id')

def copy_file(file_id, new_name):
    drive = get_drive_service()
    copied = drive.files().copy(fileId=file_id, body={'name': new_name}).execute()
    return copied.get('id')
