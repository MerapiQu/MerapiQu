<?php

namespace App\HttpSystem;

enum HTTP_CONTENT: string
{
    case HTML = "text/html";
    case JSON = "application/json";
    case XML = "application/xml";
    case TEXT = "text/plain";
    case JPEG = "image/jpeg";
    case PNG = "image/png";
    case PDF = "application/pdf";
    case JAVASCRIPT = "application/javascript";
    case CSS = "text/css";
    case CSV = "text/csv";
    case FORM_URLENCODED = "application/x-www-form-urlencoded";
    case MULTIPART_FORM_DATA = "multipart/form-data";
    case GIF = "image/gif";
    case BMP = "image/bmp";
    case ICO = "image/x-icon";
    case SVG = "image/svg+xml";
    case WEBP = "image/webp";
    case MP3 = "audio/mpeg";
    case MP4 = "video/mp4";
    case OGG = "audio/ogg";
    case WAV = "audio/wav";
    case WEBM = "video/webm";
    case ZIP = "application/zip";
    case GZIP = "application/gzip";
    case TAR = "application/x-tar";
    case RAR = "application/vnd.rar";
}
