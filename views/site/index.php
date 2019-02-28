<?php

/* @var $this yii\web\View */
/* @var $array app\models\Staff[] */

use yii\helpers\Html;
use yii\helpers\Url;


$this->title = \Yii::t('app', 'Test');
?>
<div class="site-index">

    <div class="jumbotron">
        <h2 class="text-uppercase text-danger">
            <b>
            <?= Yii::t('app', 'Relation of employees with departments') ?>
            </b>
        </h2>
    </div>


    <div class="body-content">
        <?php if(is_array($array)){ ?>
        <table class="table table-striped">
            <thead>
            <tr>
                <?php foreach ($array[0] as $vl): ?>
                    <th><?= $vl ?></th>
                <?php endforeach ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($array as $i => $item): ?>
                <tr>
                    <?php if ($i === 0) continue; ?>

                    <?
                    foreach ($item as $id => $vl) {

                        if ($id === 0) {
                            echo '<th scope="row">' . $vl . '</th>';
                            continue;
                        }

                        if ($vl == true) {
                            echo '<td>    <span class="label btn-success"><i class="glyphicon glyphicon glyphicon-ok"></i></span></td>';
                            continue;
                        }

                        echo '<td>' . $vl . '</td>';

                    }

                    ?>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
        <?php }else{?>
            <div class="alert alert-danger text-center" role="alert">
                <?= Html::a( \Yii::t('app', 'It is necessary to add data of employees and departments.'), ['/staff/index'], ['class' => '']); ?>
            </div>
        <?php } ?>

    </div>
</div>
