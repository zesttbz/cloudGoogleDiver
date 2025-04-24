import os
from app import create_app  # import đúng nơi có create_app()

app = create_app()  # phải GÁN vào biến app trước

if __name__ == "__main__":
    port = int(os.environ.get("PORT", 5000))
    app.run(host="0.0.0.0", port=port)
