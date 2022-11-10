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

    'accepted'             => 'O :attribute deve ser aceito.', //The :attribute must be accepted.
    'active_url'           => 'O :attribute não é uma URL válida.', //The :attribute is not a valid URL.
    'after'                => 'O :attribute deve ser uma data após :date.', //The :attribute must be a date after :date.
    'after_or_equal'       => 'O :attribute deve ser uma data após ou igual a :date.', //The :attribute must be a date after or equal to :date.
    'alpha'                => 'O :attribute só pode conter letras.', //The :attribute may only contain letters.
    'alpha_dash'           => 'O :attribute só pode conter letras, números, traços e underscores.', //The :attribute may only contain letters, numbers, dashes and underscores.
    'alpha_num'            => 'O :attribute só pode conter letras e números.', //The :attribute may only contain letters and numbers.
    'array'                => 'O :attribute deve ser um vetor.', //The :attribute must be an array.
    'before'               => 'O :attribute deve ser uma data antes :date.', //The :attribute must be a date before :date.
    'before_or_equal'      => 'O :attribute deve ser uma data antes ou igual a :date.', //The :attribute must be a date before or equal to :date.
    'between'              => [
        'numeric' => 'O :attribute deve ser entre :min e :max.', //The :attribute must be between :min and :max.
        'file'    => 'O :attribute deve ser entre :min e :max kilobytes.', //The :attribute must be between :min and :max kilobytes.
        'string'  => 'O :attribute deve ser entre :min e :max characters.', //The :attribute must be between :min and :max characters.
        'array'   => 'O :attribute deve estar entre os itens :min e :max.', //The :attribute must have between :min and :max items.
    ],
    'boolean'              => 'O :attribute campo deve ser Verdadeiro ou Falso.', //The :attribute field must be true or false.
    'confirmed'            => 'O :attribute confirmação não corresponde.', //The :attribute confirmation does not match.
    'date'                 => 'O :attribute não é uma data válida.', //The :attribute is not a valid date.
    'date_format'          => 'O :attribute não corresponde ao formato :format.', //The :attribute does not match the format :format.
    'different'            => 'O :attribute e :other deve ser diferente.', //The :attribute and :other must be different.
    'digits'               => 'O :attribute deve ter :digits digitos.', //The :attribute must be :digits digits.
    'digits_between'       => 'O :attribute deve estar entre:min e :max digitos.', //The :attribute must be between :min and :max digits.
    'dimensions'           => 'O :attribute tem dimensões de imagem inválidas.', //The :attribute has invalid image dimensions.
    'distinct'             => 'O :attribute campo tem um valor duplicado.', //The :attribute field has a duplicate value.
    'email'                => 'O :attribute deve ser um endereço de e-mail válido.', //The :attribute must be a valid email address.
    'exists'               => 'O :attribute selecionado é inválido.', //The selected :attribute is invalid.
    'file'                 => 'O :attribute deve ser um arquivo.', //The :attribute must be a file.'
    'filled'               => 'O :attribute campo deve ter um valor.', //The :attribute field must have a value.
    'gt'                   => [
        'numeric' => 'O :attribute deve ser maior que :value.', //The :attribute must be greater than :value.
        'file'    => 'O :attribute deve ser maior que :value kilobytes.', //The :attribute must be greater than :value kilobytes.
        'string'  => 'O :attribute deve ser maior que :value characteres.', //The :attribute must be greater than :value characters.
        'array'   => 'O :attribute deve ter mais do que :value itens.', //The :attribute must have more than :value items.
    ],
    'gte'                  => [
        'numeric' => 'O :attribute deve ser maior ou igual :value.', //The :attribute must be greater than or equal :value.
        'file'    => 'O :attribute deve ser maior ou igual :value kilobytes.', //The :attribute must be greater than or equal :value kilobytes.
        'string'  => 'O :attribute deve ser maior ou igual :value characteres.', //The :attribute must be greater than or equal :value characters.
        'array'   => 'O :attribute deve ter :value itens ou mais.', //The :attribute must have :value items or more.
    ],
    'image'                => 'O :attribute deve ser uma imagem.', //The :attribute must be an image.
    'in'                   => 'O :attribute selecionado é inválido.', //The selected :attribute is invalid.
    'in_array'             => 'O campo :attribute não existe em :other.', //The :attribute field does not exist in :other.
    'integer'              => 'O :attribute deve ser um inteiro.', //The :attribute must be an integer.
    'ip'                   => 'O :attribute deve ser um endereço IP válido.', //The :attribute must be a valid IP address.
    'ipv4'                 => 'O :attribute deve ser um endereço IPv4 válido.', //The :attribute must be a valid IPv4 address.
    'ipv6'                 => 'O :attribute deve ser um endereço IPv6 válido.', //The :attribute must be a valid IPv6 address.
    'json'                 => 'O :attribute deve ser uma string JSON válida.', //The :attribute must be a valid JSON string.
    'lt'                   => [
        'numeric' => 'O :attribute deve ser menor que :value.', //The :attribute must be less than :value.
        'file'    => 'O :attribute deve ser menor que :value kilobytes.', //The :attribute must be less than :value kilobytes.
        'string'  => 'O :attribute deve ser menor que :value characteres.', //The :attribute must be less than :value characters.
        'array'   => 'O :attribute deve ter menos de :value itens.', //The :attribute must have less than :value items.
    ],
    'lte'                  => [
        'numeric' => 'O :attribute deve ser menor ou igual :value.', //The :attribute must be less than or equal :value.
        'file'    => 'O :attribute deve ser menor ou igual :value kilobytes.', //The :attribute must be less than or equal :value kilobytes.
        'string'  => 'O :attribute deve ser menor ou igual :value characteres.', //The :attribute must be less than or equal :value characters.
        'array'   => 'O :attribute não deve ter mais do que :value itens.', //The :attribute must not have more than :value items.
    ],
    'max'                  => [
        'numeric' => 'O :attribute pode não ser maior que :max.', //The :attribute may not be greater than :max.
        'file'    => 'O :attribute pode não ser maior que :max kilobytes.', //The :attribute may not be greater than :max kilobytes.
        'string'  => 'O :attribute pode não ser maior que :max characteres.', //The :attribute may not be greater than :max characters.
        'array'   => 'O :attribute pode não ter mais do que :max itens.', //The :attribute may not have more than :max items.
    ],
    'mimes'                => 'O :attribute deve ser um arquivo do tipo: :values.', //The :attribute must be a file of type: :values.
    'mimetypes'            => 'O :attribute deve ser um arquivo do tipo: :values.', //The :attribute must be a file of type: :values.
    'min'                  => [
        'numeric' => 'O :attribute deve ser pelo menos :min.', //The :attribute must be at least :min.
        'file'    => 'O :attribute deve ser pelo menos :min kilobytes.', //The :attribute must be at least :min kilobytes.
        'string'  => 'O :attribute deve ser pelo menos :min characteres.', //The :attribute must be at least :min characters.
        'array'   => 'O :attribute deve ter pelo menos :min itens.', //The :attribute must have at least :min items.
    ],
    'not_in'               => 'O :attribute selecionado é inválido.', //The selected :attribute is invalid.
    'not_regex'            => 'O :attribute formato é inválido.', //The :attribute format is invalid.
    'numeric'              => 'O :attribute deve ser um número.', //The :attribute must be a number.
    'present'              => 'O campo :attribute deve estar presente.', //The :attribute field must be present.
    'regex'                => 'O :attribute formato é inválido.', //The :attribute format is invalid.
    'required'             => 'O :attribute campo é required.', //The :attribute field is required.
    'required_if'          => 'O :attribute campo é requiredo quando:other é :value.', //The :attribute field is required when :other is :value.
    'required_unless'      => 'O :attribute campo é requiredo a não ser que :other é in :values.', //The :attribute field is required unless :other is in :values.
    'required_with'        => 'O :attribute campo é requiredo quando :values é presente.',  //The :attribute field is required when :values is present.
    'required_with_all'    => 'O :attribute campo é requiredo quando :values é presente.', //The :attribute field is required when :values is present.
    'required_without'     => 'O :attribute campo é requiredo quando :values não está presente.', //The :attribute field is required when :values is not present.
    'required_without_all' => 'O :attribute campo é requiredo quando nenhum dos :values está presente.', //The :attribute field is required when none of :values are present.
    'same'                 => 'O :attribute e :other deve corresponder.',  //The :attribute and :other must match.
    'size'                 => [ 
        'numeric' => 'O :attribute deve ser :size.', //The :attribute must be :size.
        'file'    => 'O :attribute deve ser :size kilobytes.', //The :attribute must be :size kilobytes.
        'string'  => 'O :attribute deve ser :size characteres.', //The :attribute must be :size characters.
        'array'   => 'O :attribute deve conter :size itens.', //The :attribute must contain :size items.
    ],
    'string'               => 'O :attribute deve ser uma string.', //The :attribute must be a string.
    'timezone'             => 'O :attribute deve ser uma zona válida.', //The :attribute must be a valid zone.
    'unique'               => 'O :attribute já foi utilizado.', //The :attribute has already been taken.
    'uploaded'             => 'O :attribute não conseguiu fazer upload.', //The :attribute failed to upload.
    'url'                  => 'O :attribute formato é inválido.', //The :attribute format is invalid.
    'base64image' => 'O :attribute deve ser um arquivo do tipo Base 64.', //The :attribute must be a file of type Base 64.

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

