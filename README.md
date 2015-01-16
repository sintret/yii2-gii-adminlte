# yii2-gii-adminlte
yii2 framework generator code with layout template adminlte and base on kartik dynagrid

setting in your config like these following :

    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'generators' => [
            'sintret' => [
                'class' => 'sintret\gii\generators\crud\Generator',
            ],
            'sintretModel' => [
                'class' => 'sintret\gii\generators\model\Generator'
            ]
        ]
    ];
