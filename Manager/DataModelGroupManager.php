<?php
/**
 * Created by PhpStorm.
 * User: franklin
 * Date: 27/05/2017
 * Time: 01:01
 */

namespace TechPromux\DynamicQueryBundle\Manager;

use TechPromux\BaseBundle\Manager\Resource\BaseResourceManager;
use TechPromux\DynamicQueryBundle\Entity\DataModel;
use TechPromux\DynamicQueryBundle\Entity\DataModelDetail;
use TechPromux\DynamicQueryBundle\Entity\DataModelGroup;

class DataModelGroupManager extends BaseResourceManager
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
     * Get entity class name
     *
     * @return class
     */
    public function getResourceClass()
    {
        return DataModelGroup
        ::class;
    }

    /**
     * Get entity short name
     *
     * @return string
     */
    public function getResourceName()
    {
        return 'DataModelGroup';
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
     * @param DataModelGroup $object
     * @return DataModelGroup
     */
    protected function prePersistUpdate($object)
    {
        $sql_name = $this->getDatamodelManager()->getUtilDynamicQueryManager()->getFieldFunctionSQLName($object->getField(), $object->getFunction());

        $object->setName($sql_name);
        $object->setSqlName($sql_name);

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

    public function preUpdate($object)
    {
        parent::preUpdate($object); // TODO: Change the autogenerated stub

        $this->prePersistUpdate($object);

        return $object;
    }

}