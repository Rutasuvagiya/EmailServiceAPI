<?php

/**
 * Email API Endpoint
 *
 * This file contains the implementation of the email API endpoint,
 * which handles sending emails to specified recipients.
 *
 * @package    Email Service API
 * @subpackage API
 * @author     Ruta Suvagiya <ruta.suvagiya@tcs.com>
 * @version    1.0.0
 *
 *
 * @api {post} /sendEmail Send Email
 * @apiName sendEmail
 * @apiGroup Email
 * @apiVersion 1.0.0
 * @apiDescription Sends an email to the specified recipient with the provided subject and message.
 *
 * @apiHeader {String} Content-Type application/json
 *
 * @apiParam {String} template The template name which need to use in email body.
 * @apiParam {String} recipient The email address of the recipient.
 * @apiParam {Array} data An array of variables to be updated in email body and subject dynamically.
 *
 * @apiSuccess {String} status Status of the email queue process ('success' or 'failure').
 * @apiSuccess {String} message Informational message about the result.
 *
 * @apiError {String} error Error message detailing why the email could not be sent.
 *
 * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": "success",
 *       "message": "Email(s) queued successfully."
 *     }
 *
 * @apiErrorExample {json} Error-Response:
 *     HTTP/1.1 400 Bad Request
 *     {
 *       "error": "Invalid JSON data."
 *     }
 *
 *     HTTP/1.1 400 Bad Request
 *     {
 *       "error": "Unsupported content type."
 *     }
 */

require 'vendor/autoload.php';
use App\API\RequestHandler;

$requestHandler = new RequestHandler();
$requestHandler->handleRequest();
