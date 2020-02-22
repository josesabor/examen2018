<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "vuelos".
 *
 * @property int $id
 * @property string|null $codigo
 * @property int $origen_id
 * @property int $destino_id
 * @property int $compania_id
 * @property string $salida
 * @property string $llegada
 * @property float $plazas
 * @property float $precio
 *
 * @property Reservas[] $reservas
 * @property Aeropuertos $origen
 * @property Aeropuertos $destino
 * @property Companias $compania
 */
class Vuelos extends \yii\db\ActiveRecord
{
    private $_restantes = null;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vuelos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['origen_id', 'destino_id', 'compania_id', 'salida', 'llegada', 'plazas', 'precio'], 'required'],
            [['origen_id', 'destino_id', 'compania_id'], 'default', 'value' => null],
            [['origen_id', 'destino_id', 'compania_id'], 'integer'],
            [['salida', 'llegada'], 'safe'],
            [['plazas', 'precio'], 'number'],
            [['codigo'], 'string', 'max' => 6],
            [['codigo'], 'unique'],
            [['origen_id'], 'exist', 'skipOnError' => true, 'targetClass' => Aeropuertos::className(), 'targetAttribute' => ['origen_id' => 'id']],
            [['destino_id'], 'exist', 'skipOnError' => true, 'targetClass' => Aeropuertos::className(), 'targetAttribute' => ['destino_id' => 'id']],
            [['compania_id'], 'exist', 'skipOnError' => true, 'targetClass' => Companias::className(), 'targetAttribute' => ['compania_id' => 'id']],
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), ['origen.codigo'], ['destino.codigo'], ['compania.denominacion']);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codigo' => 'Codigo',
            'origen_id' => 'Origen ID',
            'destino_id' => 'Destino ID',
            'compania_id' => 'Compania ID',
            'salida' => 'Salida',
            'llegada' => 'Llegada',
            'plazas' => 'Plazas',
            'precio' => 'Precio',
            'origen.codigo' => 'Origen',
            'destino.codigo' => 'Destino',
            'compania.denominacion' => 'Compañia',
        ];
    }

    /**
     * Gets query for [[Reservas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReservas()
    {
        return $this->hasMany(Reservas::className(), ['vuelo_id' => 'id']);
    }

    /**
     * Gets query for [[Origen]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrigen()
    {
        return $this->hasOne(Aeropuertos::className(), ['id' => 'origen_id']);
    }

    /**
     * Gets query for [[Destino]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDestino()
    {
        return $this->hasOne(Aeropuertos::className(), ['id' => 'destino_id']);
    }

    /**
     * Gets query for [[Compania]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompania()
    {
        return $this->hasOne(Companias::className(), ['id' => 'compania_id']);
    }

    public function setrestantes($restantes)
    {
        $this->_restantes = $restantes;
    }

    public function getrestantes()
    {
        if ($this->_restantes === null && !$this->isNewRecord) {
            $this->setrestantes($this->plazas - $this->getReservas()->count());
        }
        return $this->_restantes;
    }

    public function find2()
    {
        return parent::find()
            ->select([
                'vuelos.*',
                'plazas - COUNT(r.id) AS restantes',
            ])
            ->joinWith(['reservas r'])
            ->groupBy('vuelos.id');
    }

    public function getAsientosLibres()
    {
        $ocupados = $this->getReservas()->select('asiento')->column();
        $total = range(1, $this->plazas);
        $libres = array_diff($total, $ocupados);
        return array_combine($libres, $libres);
    }
}
