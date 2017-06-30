<?php
/**
 * Created by PhpStorm.
 * User: franklin
 * Date: 27/05/2017
 * Time: 01:01
 */

namespace TechPromux\Bundle\DynamicQueryBundle\Manager;

use TechPromux\Bundle\BaseBundle\Manager\Resource\BaseResourceManager;
use TechPromux\Bundle\DynamicQueryBundle\Entity\DataModel;
use TechPromux\Bundle\DynamicQueryBundle\Entity\DataModelDetail;

class DataModelDetailManager extends BaseResourceManager
{

    /**
     *
     * @return string
     */
    public function getBundleName()
    {
        return 'TechPromuxDynamicQueryBundle';
    }

    /**
     * Obtiene la clase de la entidad
     *
     * @return class
     */
    public function getResourceClass()
    {
        return DataModelDetail
        ::class;
    }

    /**
     * Obtiene el nombre corto de la entidad
     *
     * @return string
     */
    public function getResourceName()
    {
        return 'DataModelDetail';
    }

    //--------------------------------------------------------------------------------------

    /**
     * @var DataModelManager
     */
    protected $datamodel_manager;

    /**
     * @return DataModelManager
     */
    public function getDatamodelManager()
    {
        return $this->datamodel_manager;
    }

    /**
     * @param DataModelManager $datamodel_manager
     * @return DataModelDetailManager
     */
    public function setDatamodelManager($datamodel_manager)
    {
        $this->datamodel_manager = $datamodel_manager;
        return $this;
    }

    //-----------------------------------------------------------------------------------------------

    public function createNewInstance()
    {
        $object = parent::createNewInstance(); // TODO: Change the autogenerated stub
        $object->setEnabled(true);
        return $object;
    }

    //-------------------------------------------------------------------------------------------------

    /**
     * @param DataModelDetail $object
     * @return DataModelDetail
     */
    protected function prePersistUpdate($object)
    {
        $sql_name = $this->getDatamodelManager()->getDynamicQueryUtilManager()->getFieldFunctionSQLName($object->getField(), $object->getFunction());
        $object->setName($sql_name);
        $object->setSqlName($sql_name);

        $type = $this->getDatamodelManager()->getDynamicQueryUtilManager()->getFieldFunctionSQLType($object->getField(), $object->getFunction());
        $classification = $this->getDatamodelManager()->getDynamicQueryUtilManager()->getIsNumberDatetimeOrString($type);

        $object->setSqlType($type);
        $object->setSqlTypeCategorization($classification);

        if (is_null($object->getFunction())) {
            $object->setFunction('');
        }
        return $object;
    }


    /**
     * @param DataModelDetail $object
     * @return DataModelDetail
     */
    public function prePersist($object)
    {
        parent::prePersist($object); // TODO: Change the autogenerated stub

        $this->prePersistUpdate($object);

        return $object;
    }

    /**
     * @param DataModelDetail $object
     * @return DataModelDetail
     */
    public function preUpdate($object)
    {
        parent::preUpdate($object); // TODO: Change the autogenerated stub

        $this->prePersistUpdate($object);

        return $object;
    }

    //---------------------------------------------------------------


}