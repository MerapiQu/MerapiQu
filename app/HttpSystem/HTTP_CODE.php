<?php

namespace App\HttpSystem;

enum HTTP_CODE: int
{
    case CONTINUE = 100;
    case SWITCHING_PROTOCOLS = 101;
    case OK = 200;
    case CREATED = 201;
    case ACCEPTED = 202;
    case NON_AUTHORITATIVE_INFORMATION = 203;
    case NO_CONTENT = 204;
    case RESET_CONTENT = 205;
    case PARTIAL_CONTENT = 206;
    case MULTIPLE_CHOICES = 300;
    case MOVED_PERMANENTLY = 301;
    case FOUND = 302; // Also known as MOVED_TEMPORARILY
    case SEE_OTHER = 303;
    case NOT_MODIFIED = 304;
    case USE_PROXY = 305;
    case TEMPORARY_REDIRECT = 307;
    case PERMANENT_REDIRECT = 308;
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case PAYMENT_REQUIRED = 402;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case METHOD_NOT_ALLOWED = 405;
    case NOT_ACCEPTABLE = 406;
    case PROXY_AUTHENTICATION_REQUIRED = 407;
    case REQUEST_TIMEOUT = 408;
    case CONFLICT = 409;
    case GONE = 410;
    case LENGTH_REQUIRED = 411;
    case PRECONDITION_FAILED = 412;
    case PAYLOAD_TOO_LARGE = 413; // Previously REQUEST_ENTITY_TOO_LARGE
    case URI_TOO_LONG = 414; // Previously REQUEST_URI_TOO_LARGE
    case UNSUPPORTED_MEDIA_TYPE = 415;
    case RANGE_NOT_SATISFIABLE = 416;
    case EXPECTATION_FAILED = 417;
    case IM_A_TEAPOT = 418; // A joke code, but part of the spec!
    case MISDIRECTED_REQUEST = 421;
    case UNPROCESSABLE_ENTITY = 422;
    case LOCKED = 423;
    case FAILED_DEPENDENCY = 424;
    case TOO_EARLY = 425;
    case UPGRADE_REQUIRED = 426;
    case PRECONDITION_REQUIRED = 428;
    case TOO_MANY_REQUESTS = 429;
    case REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    case UNAVAILABLE_FOR_LEGAL_REASONS = 451;
    case INTERNAL_SERVER_ERROR = 500;
    case NOT_IMPLEMENTED = 501;
    case BAD_GATEWAY = 502;
    case SERVICE_UNAVAILABLE = 503;
    case GATEWAY_TIMEOUT = 504;
    case HTTP_VERSION_NOT_SUPPORTED = 505;
    case VARIANT_ALSO_NEGOTIATES = 506;
    case INSUFFICIENT_STORAGE = 507;
    case LOOP_DETECTED = 508;
    case NOT_EXTENDED = 510;
    case NETWORK_AUTHENTICATION_REQUIRED = 511;

    public function reasonPhrase(): string
    {
        return match ($this) {
            self::CONTINUE => 'Continue',
            self::SWITCHING_PROTOCOLS => 'Switching Protocols',
            self::OK => 'OK',
            self::CREATED => 'Created',
            self::ACCEPTED => 'Accepted',
            self::NON_AUTHORITATIVE_INFORMATION => 'Non-Authoritative Information',
            self::NO_CONTENT => 'No Content',
            self::RESET_CONTENT => 'Reset Content',
            self::PARTIAL_CONTENT => 'Partial Content',
            self::MULTIPLE_CHOICES => 'Multiple Choices',
            self::MOVED_PERMANENTLY => 'Moved Permanently',
            self::FOUND => 'Found', // Also known as Moved Temporarily
            self::SEE_OTHER => 'See Other',
            self::NOT_MODIFIED => 'Not Modified',
            self::USE_PROXY => 'Use Proxy',
            self::TEMPORARY_REDIRECT => 'Temporary Redirect',
            self::PERMANENT_REDIRECT => 'Permanent Redirect',
            self::BAD_REQUEST => 'Bad Request',
            self::UNAUTHORIZED => 'Unauthorized',
            self::PAYMENT_REQUIRED => 'Payment Required',
            self::FORBIDDEN => 'Forbidden',
            self::NOT_FOUND => 'Not Found',
            self::METHOD_NOT_ALLOWED => 'Method Not Allowed',
            self::NOT_ACCEPTABLE => 'Not Acceptable',
            self::PROXY_AUTHENTICATION_REQUIRED => 'Proxy Authentication Required',
            self::REQUEST_TIMEOUT => 'Request Timeout',
            self::CONFLICT => 'Conflict',
            self::GONE => 'Gone',
            self::LENGTH_REQUIRED => 'Length Required',
            self::PRECONDITION_FAILED => 'Precondition Failed',
            self::PAYLOAD_TOO_LARGE => 'Payload Too Large', // Previously Request Entity Too Large
            self::URI_TOO_LONG => 'URI Too Long', // Previously Request URI Too Large
            self::UNSUPPORTED_MEDIA_TYPE => 'Unsupported Media Type',
            self::RANGE_NOT_SATISFIABLE => 'Range Not Satisfiable',
            self::EXPECTATION_FAILED => 'Expectation Failed',
            self::IM_A_TEAPOT => "I'm a teapot",
            self::MISDIRECTED_REQUEST => 'Misdirected Request',
            self::UNPROCESSABLE_ENTITY => 'Unprocessable Entity',
            self::LOCKED => 'Locked',
            self::FAILED_DEPENDENCY => 'Failed Dependency',
            self::TOO_EARLY => 'Too Early',
            self::UPGRADE_REQUIRED => 'Upgrade Required',
            self::PRECONDITION_REQUIRED => 'Precondition Required',
            self::TOO_MANY_REQUESTS => 'Too Many Requests',
            self::REQUEST_HEADER_FIELDS_TOO_LARGE => 'Request Header Fields Too Large',
            self::UNAVAILABLE_FOR_LEGAL_REASONS => 'Unavailable For Legal Reasons',
            self::INTERNAL_SERVER_ERROR => 'Internal Server Error',
            self::NOT_IMPLEMENTED => 'Not Implemented',
            self::BAD_GATEWAY => 'Bad Gateway',
            self::SERVICE_UNAVAILABLE => 'Service Unavailable',
            self::GATEWAY_TIMEOUT => 'Gateway Timeout',
            self::HTTP_VERSION_NOT_SUPPORTED => 'HTTP Version Not Supported',
            self::VARIANT_ALSO_NEGOTIATES => 'Variant Also Negotiates',
            self::INSUFFICIENT_STORAGE => 'Insufficient Storage',
            self::LOOP_DETECTED => 'Loop Detected',
            self::NOT_EXTENDED => 'Not Extended',
            self::NETWORK_AUTHENTICATION_REQUIRED => 'Network Authentication Required',
        };
    }

    public static function fromCode(int $code): HTTP_CODE|null
    {
        return match ($code) {
            self::CONTINUE->value => self::CONTINUE,
            self::SWITCHING_PROTOCOLS->value => self::SWITCHING_PROTOCOLS,
            self::OK->value => self::OK,
            self::CREATED->value => self::CREATED,
            self::ACCEPTED->value => self::ACCEPTED,
            self::NON_AUTHORITATIVE_INFORMATION->value => self::NON_AUTHORITATIVE_INFORMATION,
            self::NO_CONTENT->value => self::NO_CONTENT,
            self::RESET_CONTENT->value => self::RESET_CONTENT,
            self::PARTIAL_CONTENT->value => self::PARTIAL_CONTENT,
            self::MULTIPLE_CHOICES->value => self::MULTIPLE_CHOICES,
            self::MOVED_PERMANENTLY->value => self::MOVED_PERMANENTLY,
            self::FOUND->value => self::FOUND,
            self::SEE_OTHER->value => self::SEE_OTHER,
            self::NOT_MODIFIED->value => self::NOT_MODIFIED,
            self::USE_PROXY->value => self::USE_PROXY,
            self::TEMPORARY_REDIRECT->value => self::TEMPORARY_REDIRECT,
            self::PERMANENT_REDIRECT->value => self::PERMANENT_REDIRECT,
            self::BAD_REQUEST->value => self::BAD_REQUEST,
            self::UNAUTHORIZED->value => self::UNAUTHORIZED,
            self::PAYMENT_REQUIRED->value => self::PAYMENT_REQUIRED,
            self::FORBIDDEN->value => self::FORBIDDEN,
            self::NOT_FOUND->value => self::NOT_FOUND,
            self::METHOD_NOT_ALLOWED->value => self::METHOD_NOT_ALLOWED,
            self::NOT_ACCEPTABLE->value => self::NOT_ACCEPTABLE,
            self::PROXY_AUTHENTICATION_REQUIRED->value => self::PROXY_AUTHENTICATION_REQUIRED,
            self::REQUEST_TIMEOUT->value => self::REQUEST_TIMEOUT,
            self::CONFLICT->value => self::CONFLICT,
            self::GONE->value => self::GONE,
            self::LENGTH_REQUIRED->value => self::LENGTH_REQUIRED,
            self::PRECONDITION_FAILED->value => self::PRECONDITION_FAILED,
            self::PAYLOAD_TOO_LARGE->value => self::PAYLOAD_TOO_LARGE,
            self::URI_TOO_LONG->value => self::URI_TOO_LONG,
            self::UNSUPPORTED_MEDIA_TYPE->value => self::UNSUPPORTED_MEDIA_TYPE,
            self::RANGE_NOT_SATISFIABLE->value => self::RANGE_NOT_SATISFIABLE,
            self::EXPECTATION_FAILED->value => self::EXPECTATION_FAILED,
            self::IM_A_TEAPOT->value => self::IM_A_TEAPOT,
            self::MISDIRECTED_REQUEST->value => self::MISDIRECTED_REQUEST,
            self::UNPROCESSABLE_ENTITY->value => self::UNPROCESSABLE_ENTITY,
            self::LOCKED->value => self::LOCKED,
            self::FAILED_DEPENDENCY->value => self::FAILED_DEPENDENCY,
            self::TOO_EARLY->value => self::TOO_EARLY,
            self::UPGRADE_REQUIRED->value => self::UPGRADE_REQUIRED,
            self::PRECONDITION_REQUIRED->value => self::PRECONDITION_REQUIRED,
            self::TOO_MANY_REQUESTS->value => self::TOO_MANY_REQUESTS,
            self::REQUEST_HEADER_FIELDS_TOO_LARGE->value => self::REQUEST_HEADER_FIELDS_TOO_LARGE,
            self::UNAVAILABLE_FOR_LEGAL_REASONS->value => self::UNAVAILABLE_FOR_LEGAL_REASONS,
            self::INTERNAL_SERVER_ERROR->value => self::INTERNAL_SERVER_ERROR,
            self::NOT_IMPLEMENTED->value => self::NOT_IMPLEMENTED,
            self::BAD_GATEWAY->value => self::BAD_GATEWAY,
            self::SERVICE_UNAVAILABLE->value => self::SERVICE_UNAVAILABLE,
            self::GATEWAY_TIMEOUT->value => self::GATEWAY_TIMEOUT,
            self::HTTP_VERSION_NOT_SUPPORTED->value => self::HTTP_VERSION_NOT_SUPPORTED,
            self::VARIANT_ALSO_NEGOTIATES->value => self::VARIANT_ALSO_NEGOTIATES,
            self::INSUFFICIENT_STORAGE->value => self::INSUFFICIENT_STORAGE,
            self::LOOP_DETECTED->value => self::LOOP_DETECTED,
            self::NOT_EXTENDED->value => self::NOT_EXTENDED,
            self::NETWORK_AUTHENTICATION_REQUIRED->value => self::NETWORK_AUTHENTICATION_REQUIRED,
            default => null,
        };
    }
}
