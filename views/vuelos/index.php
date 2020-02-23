<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\VuelosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Vuelos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vuelos-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Vuelos', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]);
    ?>
    <?php Yii::trace($dataProvider->getModels()) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'codigo',
            'origen.codigo',
            'destino.codigo',
            'compania.denominacion',
            'salida:datetime',
            'llegada:datetime',
            'plazas',
            'restantes',
            'precio',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{reservar}',
                'buttons' => [
                    'reservar' => function ($url, $model, $key) {
                        return Html::a('Reservar', [
                            'reservas/create',
                            'vuelo_id' => $model->id
                        ], ['class' => 'btn-sm btn-success']);
                    },
                ],
            ],
        ],
    ]); ?>


</div>