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

    'accepted'             => ':attribute trebuie să fie acceptat.',
    'active_url'           => ':attribute nu este un URL valid.',
    'after'                => ':attribute trebuie să aibe data după :date .',
    'after_or_equal'       => ':attribute trebuie să aibe data după sau egală cu :data . ',
    'alpha'                => ':attribute poate să conțină doar litere.',
    'alpha_dash'           => ':attribute poate să conțină doar litere, numere, liniuțe și subliniere.',
    'alpha_num'            => ':attribute poate să conțină doar litere și numere.',
    'array'                => ':attribute trebuie să fie o înșiruire.',
    'before'               => ':attribute trebuie să aibe data înainte de :data .',
    'before_or_equal'      => ':attribute trebuie să aibe data înainte sau egală cu :data .',
    'between'              => [
        'numeric' => ':attribute trebuie să fie între :min și :max .',
        'file'    => ':attribute trebuie să fie între :min și :max kilobyți',
        'string'  => ':attribute trebuie să fie între :min și :max caractere.',
        'array'   => ':attribute trebuie să fie între :min și :max articole.',
    ],
    'boolean'              => 'Câmpul de :attribute trebuie să fie adevărat sau fals.',
    'confirmed'            => 'Confirmarea de :attribute nu se potrivește.',
    'date'                 => ':attribute nu e are o data validă.',
    'date_format'          => ':attribute nu seamănă cu formatul :format .',
    'different'            => ':attribute și :other trebuie să fie diferite.',
    'digits'               => ':attribute trebuie să fie :digits cifre.',
    'digits_between'       => ':attribute trebuie să fie între :min și :max cifre.',
    'dimensions'           => ':attribute are imagine cu dimensiuni invalide.',
    'distinct'             => ':attribute are câmpuri cu valoare duplicată.',
    'email'                => ':attribute trebuie să fie o adresă de email validă.',
    'exists'               => ':attribute selectata trebuie sa fie valida',
    'file'                 => ':attribute trebuie să fie un fișier.',
    'filled'               => ':attribute trebuie să fie o valoare.',
    'gt'                   => [
        'numeric' => ':attribute trebuie să fie mai mare ca :value .',
        'file'    => ':attribute trebuie să fie mai mare ca :value kilobyți',
        'string'  => ':attribute trebuie să fie mai mare ca :value caractere.',
        'array'   => ':attribute trebuie să aibe :value cifre sau mai multe',
    ],
    'gte'                  => [
        'numeric' => ':attribute trebuie să fie mai mare sau egală ca :value ',
        'file'    => ':attribute trebuie să fie mai mare sau egală ca :value kilobyți',
        'string'  => ':attribute trebuie să fie mai mare sau egală ca :value caractere',
        'array'   => ':attribute trebuie să aibe :value cifre sau mai multe.',
    ],
    'image'                => ':attribute să fie o imagine.',
    'in'                   => ':attribute selectat este invalid.',
    'in_array'             => 'Câmpul :attribute nu se potrivește cu :other .',
    'integer'              => ':attribute trebuie să fie un întreg.',
    'ip'                   => ':attribute trebuie să fie o adresă IP valid.',
    'ipv4'                 => ':attribute trebuie să fie o adresă IPv4.',
    'ipv6'                 => ':attribute trebuie  fie o  IPv6.',
    'json'                 => ':attribute trebuie  fie un  JSON valid.',
    'lt'                   => [
        'numeric' => ':attribute trebuie fie :value .',
        'file'    => ':attribute trebuie fie :value kilobyți..',
        'string'  => ':attribute trebuie fie :value caractere.',
        'array'   => ':attribute trebuie aibe :value cifre puține.',
    ],
    'lte'                  => [
        'numeric' => ':attribute trebuie fie :value .',
        'file'    => ':attribute trebuie fie :value kilobyți.',
        'string'  => ':attribute trebuie fie :value caractere.',
        'array'   => ':attribute trebuie nu aibe :value cifre puține.',
    ],
    'max'                  => [
        'numeric' => ':attribute nu poate fie :max .',
        'file'    => ':attribute nu poate fie :max kilobyți.',
        'string'  => ':attribute nu poate fie :max kilobyți.',
        'array'   => ':attribute nu poate fie :max kilobyți.',
    ],
    'mimes'                => ':attribute trebuie aibe fișierul de: :values .',
    'mimetypes'            => ':attribute trebuie aibe fișierul de: :values .',
    'min'                  => [
        'numeric' => ':attribute trebuie fie cel :min',
        'file'    => ':attribute trebuie fie cel :min kilobyți',
        'string'  => ':attribute trebuie fie cel :min caractere.',
        'array'   => ':attribute trebuie aibe cel :min articole.',
    ],
    'not_in'               => ':attribute selectat nu este valid.',
    'not_regex'            => 'Formatul :attribute nu este valid.',
    'numeric'              => ':attribute trebuie fie un număr.',
    'present'              => 'Câmpul :attribute trebuie  fie prezent.',
    'regex'                => 'Formatul :attribute este invalid.',
    'required'             => 'Câmpul :attribute trebuie fie completat.',
    'required_if'          => 'Câmpul :attribute trebuie fie completat când :other este :value .',
    'required_unless'      => 'Câmpul :attribute trebuie fie completat nu :other este :values.',
    'required_with'        => 'Câmpul :attribute trebuie fie completat când :value este prezent',
    'required_with_all'    => 'Câmpul :attribute trebuie să fie completat când :values este prezentă.',
    'required_without'     => 'Câmpul :attribute trebuie să fie completat când :values nu este prezentă.',
    'required_without_all' => 'Câmpul :attribute trebuie să fie completat când nici o :values sunt prezente.',
    'same'                 => ':attribute și :other trebuie să se potrivească.',
    'size'                 => [
        'numeric' => ':attribute trebuie să fie :size .',
        'file'    => ':attribute trebuie să fie :size kilobiți.',
        'string'  => ':attribute trebuie să fie :size caractere.',
        'array'   => ':attribute trebuie să conțină :size articole.',
    ],
    'string'               => ':attribute trebuie să fie un șir.',
    'timezone'             => ':attribute trebuie să fie o zonă validă.',
    'unique'               => ':attribute a fost luat deja',
    'uploaded'             => ':attribute a eșuat să se încarce.',
    'url'                  => 'Formatul :attribute este invalid.',
    'base64image' => ':attribute trebuie să fie un fișier de tipul Base 64.',

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
