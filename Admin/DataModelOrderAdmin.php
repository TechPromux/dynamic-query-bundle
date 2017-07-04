<?php

namespace TechPromux\Bundle\DynamicQueryBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use TechPromux\Bundle\BaseBundle\Admin\Resource\BaseResourceAdmin;
use TechPromux\Bundle\DynamicQueryBundle\Entity\DataModel;
use TechPromux\Bundle\DynamicQueryBundle\Entity\DataModelDetail;
use TechPromux\Bundle\DynamicQueryBundle\Entity\DataModelOrder;
use TechPromux\Bundle\DynamicQueryBundle\Manager\DataModelDetailManager;
use TechPromux\Bundle\DynamicQueryBundle\Manager\DataModelOrderManager;

class DataModelOrderAdmin extends BaseResourceAdmin
{
    /**
     * @return DataModelOrderManager
     */
    public function getResourceManager()
    {
        return parent::getResourceManager(); // TODO: Change the autogenerated stub
    }

    /**
     * @return DataModelOrder
     */
    public function getSubject()
    {
        return parent::getSubject(); // TODO: Change the autogenerated stub
    }

    //-----------------------------------------------------------------------------------------------


    protected function configureRoutes(\Sonata\AdminBundle\Route\RouteCollection $collection)
    {
        parent::configureRoutes($collection);

        $collection->clearExcept(array('create', 'edit', 'delete'));
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        parent::configureFormFields($formMapper);

        $object = $this->getSubject();

        $datamodel = $this->getParentFieldDescription()->getAdmin()->getSubject();
        /* @var $datamodel DataModel */

        $datamodel_manager = $this->getResourceManager()->getDatamodelManager();

        $metadata_field_manager = $datamodel_manager->getMetadataManager()->getMetadataFieldManager();

        $metadata = $datamodel->getMetadata();

        $formMapper
            ->add('position', 'hidden', array(
                'attr' => array('data-ctype' => 'datamodel-order-position',
                )
            ))
            ->add('enabled', null, array(
                'required' => false,
                'attr' => array(
                    'data-ctype' => 'datamodel-order-enabled'
                )
            ))
            ->add('field', 'entity', array(
                    'class' => $metadata_field_manager->getResourceClassShortcut(),
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $er) use ($metadata_field_manager, $metadata) {
                        $qb = $metadata_field_manager->createQueryBuilderForMetadataAndEnabledsSelection($metadata);
                        return $qb;
                    },
                    'choice_label' => 'title',
                    'group_by' => 'table.title',
                    'attr' => array(
                        'class' => 'col-md-12',
                        'style' => 'padding-left: 0px; padding-right:0px',
                        'data-ctype' => 'datamodel-order-field'),
                    "multiple" => false, "expanded" => false, 'required' => true)
            )
            ->add('function', 'choice', array(
                'choices' => $datamodel_manager->getUtilDynamicQueryManager()->getFieldFunctionsChoices(),
                'required' => false,
                'attr' => array(
                    'class' => 'col-md-12',
                    'style' => 'padding-left: 0px; padding-right:0px',
                    'placeholder' => 'datamodel.field.function.placeholder',
                    'data-ctype' => 'datamodel-order-function'
                ),
                'translation_domain' => $this->getResourceManager()->getBundleName()
            ))
            ->add('type', 'choice', array(
                'choices' => $datamodel_manager->getUtilDynamicQueryManager()->getOrderTypesChoices(),
                'required' => true,
                'expanded' => false,
                'multiple' => false,
                'attr' => array(
                    'class' => 'col-md-12',
                    'style' => 'padding-left: 0px; padding-right:0px',
                    'data-ctype' => 'datamodel-order-type'),
                'translation_domain' => $this->getResourceManager()->getBundleName()
            ));
    }

    public function getNewInstance()
    {
        $object = parent::getNewInstance(); // TODO: Change the autogenerated stub
        $object->setEnabled(true);
        return $object;
    }

    public function validate(\Sonata\CoreBundle\Validator\ErrorElement $errorElement, $object)
    {


        parent::validate($errorElement, $object);

        $errorElement
            ->with('field')
            ->assertNotBlank()
            ->end();
    }

}
