<?php
/**
 * Minimal PHPMailer-compatible SMTP mailer implementation.
 *
 * This lightweight implementation provides the subset of PHPMailer used by the
 * application to send authenticated SMTP messages without relying on Composer
 * installations, which may be unavailable in the runtime environment.
 *
 * It is not a drop-in replacement for the full PHPMailer library but mirrors
 * the public API surface required by this project (constructor signature,
 * configuration properties, and send workflow). Only basic text emails with a
 * single recipient are supported.
 */

declare(strict_types=1);

namespace PHPMailer\PHPMailer;

class Exception extends \Exception
{
}

class PHPMailer
{
    /** @var bool */
    public bool $SMTPAuth = false;
    /** @var string */
    public string $Host = 'localhost';
    /** @var int */
    public int $Port = 25;
    /** @var string|null */
    public ?string $SMTPSecure = null;
    /** @var string */
    public string $Username = '';
    /** @var string */
    public string $Password = '';
    /** @var string */
    public string $Subject = '';
    /** @var string */
    public string $Body = '';
    /** @var string */
    public string $AltBody = '';
    /** @var string */
    public string $CharSet = 'UTF-8';
    /** @var string */
    public string $ErrorInfo = '';
    /** @var string */
    public string $Hostname = '';

    /** @var string */
    private string $fromAddress = '';
    /** @var string */
    private string $fromName = '';
    /** @var array<int, array{address: string, name: string}> */
    private array $addresses = [];
    /** @var bool */
    private bool $useSMTP = false;
    /** @var bool */
    private bool $exceptions = false;
    /** @var int */
    public int $Timeout = 30;

    public function __construct(bool $exceptions = false)
    {
        $this->exceptions = $exceptions;
    }

    public function isSMTP(): void
    {
        $this->useSMTP = true;
    }

    public function setFrom(string $address, string $name = '', bool $auto = true): void
    {
        $this->fromAddress = $address;
        $this->fromName = $name;
    }

    public function addAddress(string $address, string $name = ''): void
    {
        $this->addresses[] = [
            'address' => $address,
            'name' => $name,
        ];
    }

    public function isHTML(bool $isHtml = true): void
    {
        // HTML output is not supported in this minimal implementation.
    }

    /**
     * Sends the email using a direct SMTP connection.
     */
    public function send(): bool
    {
        if (!$this->useSMTP) {
            return $this->raiseError('SMTP mode must be enabled.');
        }

        if ($this->fromAddress === '') {
            return $this->raiseError('From address must be set.');
        }

        if (!$this->addresses) {
            return $this->raiseError('No recipient addresses provided.');
        }

        $host = $this->Host;
        $port = $this->Port;
        $transport = '';

        if ($this->SMTPSecure === 'ssl') {
            $transport = 'ssl://';
        }

        $remote = $transport . $host . ':' . $port;
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
                'allow_self_signed' => false,
            ],
        ]);

        $socket = @stream_socket_client(
            $remote,
            $errorNumber,
            $errorString,
            $this->Timeout,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (!$socket) {
            return $this->raiseError(sprintf('Could not connect to SMTP host %s:%d (%s).', $host, $port, $errorString));
        }

        stream_set_timeout($socket, $this->Timeout);

        try {
            $this->assertResponse($socket, 220);
            $this->sendCommand($socket, 'EHLO ' . $this->getHelloName(), 250);

            if ($this->SMTPSecure === 'tls') {
                $this->sendCommand($socket, 'STARTTLS', 220);
                if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    throw new Exception('Unable to establish TLS encryption.');
                }
                $this->sendCommand($socket, 'EHLO ' . $this->getHelloName(), 250);
            }

            if ($this->SMTPAuth) {
                $this->sendCommand($socket, 'AUTH LOGIN', 334);
                $this->sendCommand($socket, base64_encode($this->Username), 334);
                $this->sendCommand($socket, base64_encode($this->Password), 235);
            }

            $this->sendCommand($socket, 'MAIL FROM:<' . $this->fromAddress . '>', 250);

            foreach ($this->addresses as $recipient) {
                $this->sendCommand($socket, 'RCPT TO:<' . $recipient['address'] . '>', [250, 251]);
            }

            $this->sendCommand($socket, 'DATA', 354);

            $data = $this->buildMessage();
            $this->writeData($socket, $data);
            $this->sendCommand($socket, '.', 250);

            $this->sendCommand($socket, 'QUIT', 221);
        } catch (Exception $exception) {
            fclose($socket);
            return $this->raiseError($exception->getMessage());
        }

        fclose($socket);
        return true;
    }

    /**
     * Formats the hostname for the EHLO/HELO command.
     */
    private function getHelloName(): string
    {
        if ($this->Hostname !== '') {
            return $this->Hostname;
        }

        $host = gethostname();
        return $host !== false ? $host : 'localhost';
    }

    /**
     * Builds the RFC 2822 formatted message body with headers.
     */
    private function buildMessage(): string
    {
        $date = gmdate('D, d M Y H:i:s') . ' +0000';
        $messageId = sprintf('<%s@%s>', uniqid('', true), $this->getHelloName());

        $headers = [
            'Date: ' . $date,
            'From: ' . $this->formatAddress($this->fromAddress, $this->fromName),
            'To: ' . $this->formatRecipients(),
            'Subject: ' . $this->encodeHeader($this->Subject),
            'Message-ID: ' . $messageId,
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=' . $this->CharSet,
            'Content-Transfer-Encoding: 8bit',
            'X-Mailer: MinimalPHPMailer',
        ];

        $body = $this->Body !== '' ? $this->Body : $this->AltBody;
        $body = $this->normalizeLineEndings($body);

        $lines = [];
        foreach (explode("\n", $body) as $line) {
            if (isset($line[0]) && $line[0] === '.') {
                $lines[] = '.' . $line;
            } else {
                $lines[] = $line;
            }
        }

        return implode("\r\n", $headers) . "\r\n\r\n" . implode("\r\n", $lines);
    }

    /**
     * Writes data to the SMTP socket.
     */
    private function writeData($socket, string $data): void
    {
        $length = strlen($data);
        $written = 0;

        while ($written < $length) {
            $result = fwrite($socket, substr($data, $written));
            if ($result === false) {
                throw new Exception('Failed writing to SMTP socket.');
            }
            $written += $result;
        }

        if (fwrite($socket, "\r\n") === false) {
            throw new Exception('Failed finalizing SMTP data block.');
        }
    }

    /**
     * Formats a single email address.
     */
    private function formatAddress(string $address, string $name): string
    {
        if ($name === '') {
            return $address;
        }

        $encodedName = $this->encodeHeader($name);
        return sprintf('"%s" <%s>', $encodedName, $address);
    }

    /**
     * Formats all recipients for the To header.
     */
    private function formatRecipients(): string
    {
        $formatted = [];
        foreach ($this->addresses as $recipient) {
            $formatted[] = $this->formatAddress($recipient['address'], $recipient['name']);
        }

        return implode(', ', $formatted);
    }

    /**
     * Encodes header content using quoted-printable rules for UTF-8 strings.
     */
    private function encodeHeader(string $value): string
    {
        if ($value === '') {
            return '';
        }

        if (preg_match('/^[\x20-\x7E]+$/', $value) === 1) {
            return $value;
        }

        $encoded = quoted_printable_encode($value);
        $encoded = str_replace(["?", "_"], ['=3F', '=5F'], $encoded);
        return sprintf('=?%s?Q?%s?=', $this->CharSet, $encoded);
    }

    /**
     * Normalizes line endings to LF.
     */
    private function normalizeLineEndings(string $text): string
    {
        return str_replace(["\r\n", "\r"], "\n", $text);
    }

    /**
     * Reads a response from the SMTP server and checks the code.
     *
     * @param resource $socket
     * @param int|array<int> $expectedCode
     */
    private function assertResponse($socket, int|array $expectedCode): string
    {
        $response = $this->readResponse($socket);
        $code = (int) substr($response, 0, 3);

        $expected = (array) $expectedCode;
        if (!in_array($code, $expected, true)) {
            throw new Exception(trim($response));
        }

        return $response;
    }

    /**
     * Sends a command and validates the response code.
     *
     * @param resource $socket
     * @param int|array<int> $expectedCode
     */
    private function sendCommand($socket, string $command, int|array $expectedCode): string
    {
        if (fwrite($socket, $command . "\r\n") === false) {
            throw new Exception('Failed writing command to SMTP server.');
        }

        return $this->assertResponse($socket, $expectedCode);
    }

    /**
     * Reads an SMTP response, handling multi-line replies.
     *
     * @param resource $socket
     */
    private function readResponse($socket): string
    {
        $data = '';
        while (($line = fgets($socket, 515)) !== false) {
            $data .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }

        if ($data === '') {
            throw new Exception('No response from SMTP server.');
        }

        return $data;
    }

    /**
     * Handles errors depending on the exception configuration.
     */
    private function raiseError(string $message): bool
    {
        $this->ErrorInfo = $message;

        if ($this->exceptions) {
            throw new Exception($message);
        }

        return false;
    }
}