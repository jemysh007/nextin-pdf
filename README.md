# NextIn-PDF

NextIn-PDF is a Laravel-based application that provides a set of powerful APIs for various PDF manipulation tasks. It allows users to compress, merge, convert, and modify PDFs, along with additional features like watermarking, rotating, and repairing PDFs.

## Features

- **Compress PDFs**
- **Merge multiple PDFs**
- **Convert images to PDF**
- **Convert Office files to PDF**
- **Extract PDF to images**
- **Rotate PDFs**
- **Convert HTML to PDF**
- **Lock and Unlock PDFs**
- **Split PDFs**
- **Split and Merge PDFs**
- **Repair damaged PDFs**
- **Add watermark text or image to PDFs**

## API Endpoints

Here are the available API endpoints. For detailed documentation on the request parameters, body, and response, please explore the API through Postman:

- **POST** `/compress` – Compress a PDF
- **POST** `/merge` – Merge multiple PDFs into one
- **POST** `/image-to-pdf` – Convert images to PDF
- **POST** `/office-to-pdf` – Convert Office documents to PDF
- **POST** `/pdf-to-images` – Extract images from a PDF
- **POST** `/rotate-pdf` – Rotate a PDF
- **POST** `/html-to-pdf` – Convert HTML to PDF
- **POST** `/lock-pdf` – Lock a PDF with a password
- **POST** `/unlock-pdf` – Unlock a password-protected PDF
- **POST** `/split-pdf` – Split a PDF into individual pages
- **POST** `/split-pdf-merge` – Split a PDF and merge selected pages
- **POST** `/repair-pdf` – Repair a corrupted PDF
- **POST** `/watermark-pdf` – Add a text watermark to a PDF
- **POST** `/watermark-pdf-image` – Add an image watermark to a PDF
- **GET** `/remove-files-cron` – Remove files as part of a scheduled cleanup

## Postman Documentation

Explore the full API documentation, including detailed information on parameters, request body, and response formats using the Postman collection:

[Postman Documentation Link](https://documenter.getpostman.com/view/19085561/2s83f4HaEr)

## iLovePDF API Keys

To use the iLovePDF API services, you need to get your **public** and **secret** API keys from [iLovePDF](https://ilovepdf.com/). After obtaining the keys, add them to your `.env` file:

```dotenv
ILOVEPDF_PUBLIC_KEY="your-public-key"
ILOVEPDF_SECRET_KEY="your-secret-key"
```

## Installation

1. Clone the repository:
    ```bash
    git clone https://github.com/jemysh007/nextin-pdf.git
    ```

2. Navigate to the project directory:
    ```bash
    cd nextin-pdf
    ```

3. Install dependencies:
    ```bash
    composer install
    ```

4. Set up your `.env` file by copying the `.env.example` file:
    ```bash
    cp .env.example .env
    ```

5. Generate the application key:
    ```bash
    php artisan key:generate
    ```

6. Run migrations if applicable:
    ```bash
    php artisan migrate
    ```

7. Start the server:
    ```bash
    php artisan serve
    ```

## License

This project is open-source and available under the [MIT License](LICENSE).
