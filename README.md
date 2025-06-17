# US Driving License Verification with IDAnalyzer API in PHP

A simple open-source PHP starter project for verifying US driving licenses using the [IDAnalyzer API](https://www.idanalyzer.com/). Upload a license image and receive extracted details using IDAnalyzer's secure document verification service.

---

## ✨ Features

- Simple, clean UI for uploading license images.
- Extracts and displays key license data (name, DOB, document number, expiry).
- Designed for educational/demo use.
- Easy environment variable setup (API key via `.env`).

---

## 🚀 Quick Start

### 1. Clone the repository

```bash
git clone https://github.com/rubyat/us-dl-verification-idanalyzer.git
cd us-dl-verification-idanalyzer
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Set up your environment
Copy .env.example to .env:

```bash
cp .env.example .env
```
Get your API key from IDAnalyzer and paste it into .env.

### 4. Run locally
If using PHP built-in server:

```bash
php -S localhost:8000
```
Visit http://localhost:8000 in your browser.

## 📄 Project Structure
- `/index.php` - Main upload & verification UI
- `/.env.example` - Example environment file (never commit your real `.env`)
- `/.gitignore` - Ignores sensitive & vendor files
- `/README.md` - This file
- `/composer.json` - Composer configuration

## ⚠️ Security Warning
Never commit your real `.env` or API keys to public repos!

This project is for demo purposes and lacks full production security (no file validation/sanitization, rate limiting, etc.).