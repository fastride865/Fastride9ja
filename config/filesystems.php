<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3", "rackspace"
    |
    */

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],
        'public' => [
            'driver' => 'local',
            'root' => public_path(),
            'url' => env('APP_URL'),
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
//            'visibility' => 'public',
        ],
        'gcs' => [
            'driver' => 'gcs',
            'project_id' => 'tezgo-323505',
            'key_file' => [
                'type' => "service_account",
                'private_key_id' => "7cf39eea5fc809c7ea7ed7650986b8fc7ec9865e",
                'private_key' => "-----BEGIN PRIVATE KEY-----\nMIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDsM1MH5orlxH37\n9J3xGst4KjfPzFitbTdclXIyyjRP8UN9Os2Uuql82mK6gOKYI8SLEtdrCqr3Kq7d\n7c98oCEFTmIXw8SWZ6pa4dfK8zZCPyAwbRhXzrDLBWMLgGi6tsBRAQWuzGE9Ns4N\nMiFt9rRCMHFpstD0J6mAp7M676XTPv3ktxysob3PxmyezW9kGUSlzhrs00En5els\nSZbnn3FyJmQOW+mV/YAP3CxcpAOBJvBOXyJIW7i4EsAZV/Eeylc0w+RvQ2mPcK8J\nP1viIpqhtgXkdZfGfao+oe0B6r4SpJxgbC7XvcGX7+rlA6i4OabdXxA8xjSzGk8j\n88x7yYttAgMBAAECggEADQqWaojBoYcUV619bPsIQfW+yCPlv4mYtW1w9Tnx4noV\nOlxdTnlx8zlRmy+Tk0fiSbV8HGZl4yjBp/JB8wUaXaMiymIeQBmwL71pf0Snecfn\nfJUBc5Ovcj41ZkSsQ6bkfnR6fli9g7Y7cUIT03D8Ke1nzai4Xcq00qwvurDRrJSi\nIC101wPobcHdynHsSdqIIWBUbuWV1X45ZHa016B4Dg6ftaWHixBocNAog+OagSFi\nvtjZa5ArUsJm/J16m3pVrqLhvHuMNL/2QvgbEbm0opoBsLe1TkXRMn89gfWqI4W1\ndWVFPtICBImrUj34UXwaxc1kVBPti8Q/cTJ09O0SgQKBgQD9uEsRWCJl2tiXUOHT\nijC9BXrm9CZBEpyfFIMM1bamon/xc6zxIxvRbzIvApjmKhYMr9h3pcB0YpKxwouD\nu8bZDIMpjFu71WMetSUXQJOlgP2HVvbJa6TPs8lYIH2DI+Ktn6BOPng6YOCechLb\ngqBh68TSl4UaZpb5e4NOXBbYwQKBgQDuUrndheWmA9X85qYwXblFhOnEa/4zwjl8\nhmQkqkcM7EFCgxuN95Jxze951CUxT0qgvxAuTXSchp5RjEk+35GdAVZRKepair8R\njcVwZoAKJ0pIzdjv0d8Cxoan06dyz1s+cV22o5UzjYkQrHASeXN4zuywFPJRCzE3\noSjZksRRrQKBgAmT7XGILDRAILEFoqDDtLdN/6e8S71lIHh448GWR8DenYnV3g90\nTdaIJhLUPEVkDVUJRlit7yf1mKgROgcNDaKf8EufOs9KbOV53R6Vl31F80wqokJU\nR/J3TQCAqXxL3IDFZ93MSemaQqB4mfGjar6HkfSJN8MZYFWmrfxFSa+BAoGBAKay\nFm9sCIVmiXarnfKWm9CUdy58mwF5CNyg48sBj1Dqr3rmWY+jaztO4AJG8PzciUaG\nXbsFUltpjbNcfJ8NobxAAzAiMVnDUoHkuAU2rLhtYvgpg2O7WFGIqwcYdDdJ8nhq\nszHcma/Ff+m5s2o4qRwHIGbJP/SP740JnJkSn17BAoGBAM7hWF1QhqWyjjaYMj/B\nqU012pTpBpKK7nYnHjIqe2LeNiISTBi4v6NPxn/VeGgthJjuIFEm30TevknD9atn\n3OV6T1RiQ0toU1plGXviosOt0uXUxvvCBRDyN685NfwPADWj27RMXi8cRVKEpCWM\nd+G8QSYHQiuQI7Pv35UXsg22\n-----END PRIVATE KEY-----\n",
                'client_email' => "ms-tezgo-app-storage@tezgo-323505.iam.gserviceaccount.com",
                'client_id' => "108547967533041840729",
                'auth_uri' => "https://accounts.google.com/o/oauth2/auth",
                'token_uri' => "https://oauth2.googleapis.com/token",
                'auth_provider_x509_cert_url' => "https://www.googleapis.com/oauth2/v1/certs",
                'client_x509_cert_url' => "https://www.googleapis.com/robot/v1/metadata/x509/ms-tezgo-app-storage%40tezgo-323505.iam.gserviceaccount.com",
            ],
            'bucket' => 'ms-tezgo-app',
            'path_prefix' => "",
            'storage_api_uri' => "https://storage.googleapis.com/ms-tezgo-app/", // see: Public URLs below
            'visibility' => 'public', // optional: public|private
        ],
//        'gcs' => [
//            'driver' => 'gcs',
//            'project_id' => 'todaph',
//            'key_file' => [
//                'type' => "service_account",
//                'private_key_id' => "7cebd118c3e77c5720f3cf79fc4dd236b72f565e",
//                'private_key' => "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDmnxPkqE8pSi4J\nnfp5ti6u7cQkw7kH++y6KRBmerE1ZL8TJrv9O/oANPNkwwEBxnYuFlqB5UpJq8zr\nlQmMHo0j0QCGXhif86tNcNmI+k+6qL5J6V9K1rj76VNz5OF8UErIKNm/oy0NU+uE\n5rfduQ7w1Ux7cTniwLlzaYqA6C8ea2o/ZUlo5QtBKTlmsmWWyesJW3GG6UtTxqAn\nuNvOUzAgZ4jKO6UEYG8OYdG3tGChc/BbBNkcVEBec6Qcd6oQt545ZMQfzcDYQeOj\nM7TQrYFKCvj4lCTZE/6WR4jAy6TGkrsbNoZ+7tTqQ6FbCegXfCUUqo/+OkMO7+R7\n8O6t5wn9AgMBAAECggEAFV4xaWsH1KdNHzJ+Qfc1mZsppI9m7jyzGSeTX69oCm6w\nfo2E9whu2ESPtasUxY2WJxyGm8j7KlrK9Jv9Q7iT8rjbf9epl+5rlZQKb9TjfMPE\nR9Rh8iz1fE/I3fDbzJki6KtSS52Kn9TB+nEnIZnTLtDQkpJx3tblB/LENL/ul7aG\nlwCKm2tpL2DJfgiKbZm7KUULuD67SaudCmOYMfbfXGwPnhPpTQUTt7Hc2G9mBm2x\nTrj9+dyWW/VzFy8xcHpG1W8YEaKFuq3dPeoxat/k+0zUPgYJeyT2C90jXu5oO7Ve\njfF3W1mJhmn5lGqnEvDkjMuepxZ4dyJUZlr+UiYdAQKBgQD7UnNAV4xDKY4RzoGp\nlNkwa9BKUxye1HTxLtuUXEFP5D2UvSTWzPhRJxvxcLvvCk8zM92r4GKFVxyXLQHJ\nmOhJ6yjJoelMQXFeH8lkQdOtr2fFR/ndlDW7eXNpwS+AWE6uPfWzF1tlVsJUd5L2\nnYxdnSWwdCh6cOG71HiHH2WS/QKBgQDq6f0hEYPCIASZjBxEkK8bpPG8qjHP11yd\nZvpdgLecPb+FcO5RIjIBmm9OX5olb/1c6wWL6IphBx3BHgjA6lNhF4vsIIGhQjVA\n1clDid+edFVf5lf2S4VvhiJOPBX5kjvRcvV+/96Gu+zOWlUp7FuJt5KOVRS41cGI\njC1HwJKDAQKBgEe93ZB8bVmuvxNuM61JBEbCQA83cnAUjd1bVuse+rXnnXycEawP\njsL7uwpM+BjghINFRv4Na5JMr6in/F6j+4s+ScJlKcfO1qHbyQ2JjPeDnse+KCGM\ncVo27S8/KmQk0TEEFuMsw7ZF8etSxu0HRE5k0aFxRyzMlGOd31oTKFexAoGAUWu2\ng46ph1BHp9yrM4yeVMuPyd+Hkk1H2XqGzn+9pBa22g5xW4epo2qep6B1MgKl413G\nN17rD4RC6Nt7FzpgmedqZPZDV9w1zvoKXzFbY3VY3ftdg41be3MXUtx6lVz9BLR+\nqH0Q6Mwb4M3odLZqZ8pLrq+IliPoh2Zmj1cMsQECgYEAj9r8gVNJaJ90skrG4vvi\ngeG1tB3Pcii3Szlex0FSCgCy8YHfsu7B2QRMeOSlH/KjoEiDGSwTKAj1hSThYfQq\n/HUoIOndGLGyC8p9CZM0jGj/npkP+O6utb7Jo0LpQs7z2SgoSIp/NQue9PFCCuxX\nVfS0EpZMkxQuc2jNUof6ISw=\n-----END PRIVATE KEY-----\n",
//                'client_email' => "bucket-access@todaph.iam.gserviceaccount.com",
//                'client_id' => "102626360282940393282",
//                'auth_uri' => "https://accounts.google.com/o/oauth2/auth",
//                'token_uri' => "https://oauth2.googleapis.com/token",
//                'auth_provider_x509_cert_url' => "https://www.googleapis.com/oauth2/v1/certs",
//                'client_x509_cert_url' => "https://www.googleapis.com/robot/v1/metadata/x509/bucket-access%40todaph.iam.gserviceaccount.com",
//            ],
//            'bucket' => 'todaph',
//            'path_prefix' => "",
//            'storage_api_uri' => "https://storage.googleapis.com/todaph/", // see: Public URLs below
//            'visibility' => 'public', // optional: public|private
//        ],

    ],

];
