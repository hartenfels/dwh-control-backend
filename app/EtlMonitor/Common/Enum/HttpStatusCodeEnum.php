<?php

namespace App\EtlMonitor\Common\Enum;

/**
 * @method static HttpStatusCodeEnum Continue
 * @method static HttpStatusCodeEnum Switching_Protocols
 * @method static HttpStatusCodeEnum Processing
 * @method static HttpStatusCodeEnum OK
 * @method static HttpStatusCodeEnum Created
 * @method static HttpStatusCodeEnum Accepted
 * @method static HttpStatusCodeEnum Non_authoritative_Information
 * @method static HttpStatusCodeEnum No_Content
 * @method static HttpStatusCodeEnum Reset_Content
 * @method static HttpStatusCodeEnum Partial_Content
 * @method static HttpStatusCodeEnum Multi_Status
 * @method static HttpStatusCodeEnum Already_Reported
 * @method static HttpStatusCodeEnum IM_Used
 * @method static HttpStatusCodeEnum Multiple_Choices
 * @method static HttpStatusCodeEnum Moved_Permanently
 * @method static HttpStatusCodeEnum Found
 * @method static HttpStatusCodeEnum See_Other
 * @method static HttpStatusCodeEnum Not_Modified
 * @method static HttpStatusCodeEnum Use_Proxy
 * @method static HttpStatusCodeEnum Temporary_Redirect
 * @method static HttpStatusCodeEnum Permanent_Redirect
 * @method static HttpStatusCodeEnum Bad_Request
 * @method static HttpStatusCodeEnum Unauthorized
 * @method static HttpStatusCodeEnum Payment_Required
 * @method static HttpStatusCodeEnum Forbidden
 * @method static HttpStatusCodeEnum Not_Found
 * @method static HttpStatusCodeEnum Method_Not_Allowed
 * @method static HttpStatusCodeEnum Not_Acceptable
 * @method static HttpStatusCodeEnum Proxy_Authentication_Required
 * @method static HttpStatusCodeEnum Request_Timeout
 * @method static HttpStatusCodeEnum Conflict
 * @method static HttpStatusCodeEnum Gone
 * @method static HttpStatusCodeEnum Length_Required
 * @method static HttpStatusCodeEnum Precondition_Failed
 * @method static HttpStatusCodeEnum Payload_Too_Large
 * @method static HttpStatusCodeEnum Request_URI_Too_Long
 * @method static HttpStatusCodeEnum Unsupported_Media_Type
 * @method static HttpStatusCodeEnum Requested_Range_Not_Satisfiable
 * @method static HttpStatusCodeEnum Expectation_Failed
 * @method static HttpStatusCodeEnum Im_a_teapot
 * @method static HttpStatusCodeEnum Misdirected_Request
 * @method static HttpStatusCodeEnum Unprocessable_Entity
 * @method static HttpStatusCodeEnum Locked
 * @method static HttpStatusCodeEnum Failed_Dependency
 * @method static HttpStatusCodeEnum Upgrade_Required
 * @method static HttpStatusCodeEnum Precondition_Required
 * @method static HttpStatusCodeEnum Too_Many_Requests
 * @method static HttpStatusCodeEnum Request_Header_Fields_TooLarge
 * @method static HttpStatusCodeEnum Connection_Closed_Without_Response
 * @method static HttpStatusCodeEnum Unavailable_For_Legal_Reasons
 * @method static HttpStatusCodeEnum Client_Closed_Request
 * @method static HttpStatusCodeEnum Internal_Server_Error
 * @method static HttpStatusCodeEnum Not_Implemented
 * @method static HttpStatusCodeEnum Bad_Gateway
 * @method static HttpStatusCodeEnum Service_Unavailable
 * @method static HttpStatusCodeEnum Gateway_Timeout
 * @method static HttpStatusCodeEnum HTTP_Version_Not_Supported
 * @method static HttpStatusCodeEnum Variant_Also_Negotiates
 * @method static HttpStatusCodeEnum Insufficient_Storage
 * @method static HttpStatusCodeEnum Loop_Detected
 * @method static HttpStatusCodeEnum Not_Extended
 * @method static HttpStatusCodeEnum Network_Authentication_Required
 * @method static HttpStatusCodeEnum Network_Connect_Timeout_Error
 */
class HttpStatusCodeEnum extends Enum
{

    // 1×× Informational
    public const Continue = 100;
    public const Switching_Protocols = 101;
    public const Processing = 102;

    // 2×× Success
    public const OK = 200;
    public const Created = 201;
    public const Accepted = 202;
    public const Non_authoritative_Information = 203;
    public const No_Content = 204;
    public const Reset_Content = 205;
    public const Partial_Content = 206;
    public const Multi_Status = 207;
    public const Already_Reported = 208;
    public const IM_Used = 226;

    // 3×× Redirection
    public const Multiple_Choices = 300;
    public const Moved_Permanently = 301;
    public const Found = 302;
    public const See_Other = 303;
    public const Not_Modified = 304;
    public const Use_Proxy = 305;
    public const Temporary_Redirect = 307;
    public const Permanent_Redirect = 308;

    // 4×× Client Error
    public const Bad_Request = 400;
    public const Unauthorized = 401;
    public const Payment_Required = 402;
    public const Forbidden = 403;
    public const Not_Found = 404;
    public const Method_Not_Allowed = 405;
    public const Not_Acceptable = 406;
    public const Proxy_Authentication_Required = 407;
    public const Request_Timeout = 408;
    public const Conflict = 409;
    public const Gone = 410;
    public const Length_Required = 411;
    public const Precondition_Failed = 412;
    public const Payload_Too_Large = 413;
    public const Request_URI_Too_Long = 414;
    public const Unsupported_Media_Type = 415;
    public const Requested_Range_Not_Satisfiable = 416;
    public const Expectation_Failed = 417;
    public const Im_a_teapot = 418;
    public const Misdirected_Request = 421;
    public const Unprocessable_Entity = 422;
    public const Locked = 423;
    public const Failed_Dependency = 424;
    public const Upgrade_Required = 426;
    public const Precondition_Required = 428;
    public const Too_Many_Requests = 429;
    public const Request_Header_Fields_TooLarge = 431;
    public const Connection_Closed_Without_Response = 444;
    public const Unavailable_For_Legal_Reasons = 451;
    public const Client_Closed_Request = 499;

    // 5×× Server Error
    public const Internal_Server_Error = 500;
    public const Not_Implemented = 501;
    public const Bad_Gateway = 502;
    public const Service_Unavailable = 503;
    public const Gateway_Timeout = 504;
    public const HTTP_Version_Not_Supported = 505;
    public const Variant_Also_Negotiates = 506;
    public const Insufficient_Storage = 507;
    public const Loop_Detected = 508;
    public const Not_Extended = 510;
    public const Network_Authentication_Required = 511;
    public const Network_Connect_Timeout_Error = 599;

}
