<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ' :attribute harus diterima.',
    'active_url'           => ' :attribute bukan URL yang valid.',
    'after'                => ' :attribute harus merupakan tanggal setelah :date.',
    'after_or_equal'       => ' :attribute harus merupakan tanggal setelah atau sama dengan :date.',
    'alpha'                => ' :attribute hanya boleh mengandung huruf.',
    'alpha_dash'           => ' :attribute hanya boleh berisi huruf, angka, garis putus-putus dan garis bawah.',
    'alpha_num'            => ' :attribute hanya boleh berisi huruf dan angka.',
    'array'                => ' :attribute harus berupa array.',
    'before'               => ' :attribute harus merupakan tanggal sebelum :date.',
    'before_or_equal'      => ' :attribute harus merupakan tanggal sebelum atau sama dengan :date.',
    'between'              => [
        'numeric' => ' :attribute harus antara :min dan :max.',
        'file'    => ' :attribute harus antara :min dan :max kilobyte.',
        'string'  => ' :attribute harus antara :min dan :max karakter.',
        'array'   => ' :attribute harus ada di antara :min dan :max item.',
    ],
    'boolean'              => 'Isian :attribute harus benar atau salah.',
    'confirmed'            => 'Konfirmasi :attribute tidak sesuai.',
    'date'                 => ' :attribute bukan tanggal yang valid.',
    'date_format'          => ' :attribute tidak sesuai dengan format :format.',
    'different'            => ' :attribute dan :other harus berbeda.',
    'digits'               => ' :attribute harus berupa :digits digit.',
    'digits_between'       => ' :attribute harus antara :min dan :max digit.',
    'dimensions'           => ' :attribute memiliki dimensi gambar yang tidak benar.',
    'distinct'             => 'Isian :attribute memiliki nilai duplikat.',
    'email'                => ' :attribute harus alamat e-mail yang valid.',
    'exists'               => '  :attribute terpilih tidak valid.',
    'file'                 => ' :attribute harus berupa file.',
    'filled'               => 'Isian :attribute harus memiliki nilai.',
    'gt'                   => [
        'numeric' => ' :attribute harus lebih besar dari :value.',
        'file'    => ' :attribute harus lebih besar dari :value kilobyte.',
        'string'  => ' :attribute harus lebih besar dari :value karakter.',
        'array'   => ' :attribute harus memiliki lebih dari :value item.',
    ],
    'gte'                  => [
        'numeric' => ' :attribute harus lebih besar dari atau sama dengan :value.',
        'file'    => ' :attribute harus lebih besar dari atau sama dengan :value kilobyte.',
        'string'  => ' :attribute harus lebih besar dari atau sama dengan :value karakter.',
        'array'   => ' :attribute harus memiliki :value item atau lebih.',
    ],
    'image'                => ' :attribute harus berupa gambar.',
    'in'                   => '  :attribute terpilih tidak valid.',
    'in_array'             => 'Isian :attribute tidak ada dalam :other.',
    'integer'              => ' :attribute harus berupa integer.',
    'ip'                   => ' :attribute harus berupa IP address yang valid.',
    'ipv4'                 => ' :attribute harus berupa IPv4 address yang valid.',
    'ipv6'                 => ' :attribute harus berupa IPv6 address yang valid.',
    'json'                 => ' :attribute harus berupa JSON string yang valid.',
    'lt'                   => [
        'numeric' => ' :attribute harus kurang dari :value.',
        'file'    => ' :attribute harus kurang dari :value kilobyte.',
        'string'  => ' :attribute harus kurang dari :value karakter.',
        'array'   => ' :attribute harus memiliki kurang dari :value item.',
    ],
    'lte'                  => [
        'numeric' => ' :attribute harus kurang dari atau sama dengan :value.',
        'file'    => ' :attribute harus kurang dari atau sama dengan :value kilobyte.',
        'string'  => ' :attribute harus kurang dari atau sama dengan :value karakter.',
        'array'   => ' :attribute tidak boleh memiliki lebih dari :value item.',
    ],
    'max'                  => [
        'numeric' => ' :attribute tidak boleh lebih dari :max.',
        'file'    => ' :attribute tidak boleh lebih dari :max kilobyte.',
        'string'  => ' :attribute tidak boleh lebih dari :max karakter.',
        'array'   => ' :attribute tidak boleh memiliki lebih dari :max item.',
    ],
    'mimes'                => ' :attribute harus berupa file tipe: :values.',
    'mimetypes'            => ' :attribute harus berupa file tipe: :values.',
    'min'                  => [
        'numeric' => ' :attribute harus paling sedikit :min.',
        'file'    => ' :attribute harus paling sedikit :min kilobyte.',
        'string'  => ' :attribute harus paling sedikit :min karakter.',
        'array'   => ' :attribute harus memiliki paling sedikit :min item.',
    ],
    'not_in'               => '  :attribute terpilih tidak valid.',
    'not_regex'            => 'Format :attribute tidak valid.',
    'numeric'              => ' :attribute harus berupa nomor.',
    'present'              => 'Isian :attribute harus ada.',
    'regex'                => 'Format :attribute tidak valid.',
    'required'             => 'Isian :attribute wajib diisi.',
    'required_if'          => 'Isian :attribute wajib diisi ketika :other adalah :value.',
    'required_unless'      => 'Isian :attribute wajib diisi kecuali :other di :values.',
    'required_with'        => 'Isian :attribute wajib diisi ketika :values ada.',
    'required_with_all'    => 'Isian :attribute wajib diisi ketika :values ada.',
    'required_without'     => 'Isian :attribute wajib diisi ketika :values tidak ada.',
    'required_without_all' => 'Isian :attribute wajib diisi ketika tidak ada :values yang muncul.',
    'same'                 => 'Isian :attribute dan :other harus sesuai.',
    'size'                 => [
        'numeric' => ' :attribute harus :size.',
        'file'    => ' :attribute harus :size kilobyte.',
        'string'  => ' :attribute harus :size karakter.',
        'array'   => ' :attribute harus mengandung :size item.',
    ],
    'string'               => ' :attribute harus berupa string.',
    'timezone'             => ' :attribute harus merupakan zona yang valid.',
    'unique'               => ' :attribute sudah diambil.',
    'uploaded'             => ' :attribute gagal diupload.',
    'url'                  => 'Format :attribute tidak valid.',
    'base64image' => ' :attribute harus berupa file tipe Base 64.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
