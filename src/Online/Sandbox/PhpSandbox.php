<?php

declare(strict_types=1);

namespace PhpSchool\Website\Online\Sandbox;

use PhpSchool\PhpWorkshop\Utils\System;

class PhpSandbox
{
    public const DISABLED_FUNCTIONS = [
        'exec',
        'passthru',
        'shell_exec',
        'system',
        'proc_open',
        'popen',
        'pcntl_exec',
        'pcntl_fork',
        'pcntl_rfork',
        'pcntl_forkx',
        'posix_kill',
        'posix_setsid',
        'posix_setpgid',
        'posix_setuid',
        'posix_seteuid',
        'posix_setgid',
        'posix_setegid',
        'posix_mkfifo',
        'posix_mknod',
        'putenv',
        'mail',
        'mb_send_mail',
        'error_log',
        'dl',
        'fsockopen',
        'pfsockopen',
        'stream_socket_client',
        'socket_connect',
        'socket_sendto',
        'socket_sendmsg',
        'curl_exec',
        'curl_multi_exec',
        'ftp_connect',
        'ftp_ssl_connect',
        'imap_open',
        'imap_mail',
        'ldap_connect',
        'mysqli_connect',
        'mysqli_real_connect',
        'dns_get_record',
        'dns_get_mx',
        'dns_check_record',
        'gethostbyname',
        'gethostbynamel',
        'getmxrr',
        'checkdnsrr',
        'link',
        'symlink',
    ];

    public function __construct(
        private readonly string $memoryLimit = '128M',
        private readonly int $maxExecutionTime = 10,
    ) {}

    /**
     * @param array<string> $readablePaths
     * @return list<string>
     */
    public function iniArguments(string $rootDirectory, array $readablePaths = []): array
    {
        $settings = [
            'open_basedir' => implode(PATH_SEPARATOR, array_unique([$rootDirectory, ...$readablePaths])),
            'disable_functions' => implode(',', self::DISABLED_FUNCTIONS),
            'memory_limit' => $this->memoryLimit,
            'max_execution_time' => (string) $this->maxExecutionTime,
            'allow_url_fopen' => '0',
            'allow_url_include' => '0',
            'enable_dl' => '0',
            'ffi.enable' => '0',
            'auto_prepend_file' => '',
            'auto_append_file' => '',
            'sys_temp_dir' => $rootDirectory,
            'upload_tmp_dir' => $rootDirectory,
            'session.save_path' => $rootDirectory,
            'expose_php' => '0',
        ];

        $arguments = [];
        foreach ($settings as $name => $value) {
            $arguments[] = '-d';
            $arguments[] = sprintf('%s=%s', $name, $value);
        }

        return $arguments;
    }

    /**
     * @param array<string, string|int> $variables
     * @return array<string, string|int|false>
     */
    public function environment(array $variables = []): array
    {
        $inherited = array_merge(
            array_keys(getenv()),
            array_keys($_ENV),
            array_keys(array_filter($_SERVER, 'is_string')),
        );

        $cleared = [];
        foreach ($inherited as $name) {
            $cleared[(string) $name] = false;
        }

        return $variables + $cleared;
    }

    /**
     * @param array<string> $arguments
     * @return list<string>
     */
    public function readablePathsFromArguments(array $arguments): array
    {
        $tempDir = rtrim(System::tempDir(), '/') . '/';

        $paths = [];
        foreach ($arguments as $argument) {
            $candidate = (string) preg_replace('/^sqlite:/', '', $argument);

            if (!str_starts_with($candidate, '/')) {
                continue;
            }

            $realPath = realpath($candidate);

            if ($realPath === false) {
                $realPath = realpath(dirname($candidate));
            }

            if ($realPath !== false && str_starts_with($realPath, $tempDir)) {
                $paths[] = $realPath;
            }
        }

        return array_values(array_unique($paths));
    }
}
