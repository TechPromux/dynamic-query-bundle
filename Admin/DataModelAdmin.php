<?php

namespace TechPromux\Bundle\DynamicQueryBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use TechPromux\Bundle\BaseBundle\Admin\Resource\BaseResourceAdmin;
use TechPromux\Bundle\DynamicQueryBundle\Entity\DataModel;
use TechPromux\Bundle\DynamicQueryBundle\Manager\DataModelManager;
use TechPromux\Bundle\DynamicQueryBundle\Manager\MetadataManager;

class DataModelAdmin extends BaseResourceAdmin
{
    /**
     *
     * @return string
     */
    public function getResourceManagerID()
    {
        return 'techpromux_dynamic_query.manager.datamodel';
    }

    /**
     * @return DataModelManager
     */
    public function getResourceManager()
    {
        return parent::getResourceManager(); // TODO: Change the autogenerated stub
    }
    //----------------------------------------------------------------------------------

    protected $accessMapping = array(
        'copy' => 'COPY',
        'execute' => 'EXECUTE',
    );

    /**
     *
     * @return DataModel
     */
    public function getSubject()
    {
        return parent::getSubject();
    }

    protected function configureRoutes(\Sonata\AdminBundle\Route\RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->add('copy', $this->getRouterIdParameter() . '/copy');
        $collection->add('execute', $this->getRouterIdParameter() . '/execute');
    }

    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        if (!$this->hasAccess('edit')) {
            $query->andWhere($query->getRootAlias() . '.enabled = 1');
        }
        return $query;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        if (!$this->isGranted('EDIT')) {
            $datagridMapper
                ->add('title', null, array(
                    'global_search' => true
                ));
        } else {
            parent::configureDatagridFilters($datagridMapper);
            $datagridMapper
                ->add('title', null, array(
                    'global_search' => true
                ))
                ->add('description', null, array(
                    'global_search' => false
                ));
        }
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {

        $listMapper->addIdentifier('name', null, array(
            'row_align' => 'left',
        ));
        $listMapper->addIdentifier('title', null, array(
            'row_align' => 'left',
        ));

        if ($this->hasAccess('edit')) {
            $listMapper->
            add('metadata.datasource', null, array(
                'row_align' => 'left',
                'header_style' => 'width: 15%',
                'route' => array(
                    'name' => '__'
                ),
            ))
                //->add('description')
                ->add('metadata', null, array(
                    'row_align' => 'left',
                    'header_style' => 'width: 25%',
                ))
                ->add('enabled', null, array('editable' => true,
                    'row_align' => 'center',
                    'header_style' => 'width: 100px',
                ));
        }


        parent::configureListFields($listMapper);

        $listMapper->add('_action', 'actions', array(
            'label' => ('Actions'),
            'row_align' => 'right',
            'header_style' => 'width: 190px',
            'actions' => array(
                'edit' => array(),
                'execute' => array(
                    'template' => 'TechPromuxBaseBundle:Admin:CRUD/list__action_execute.html.twig'
                ),
                'copy' => array(
                    'template' => 'TechPromuxBaseBundle:Admin:CRUD/list__action_copy.html.twig',
                    'ask_confirmation' => true
                ),
                'delete' => array(),
            )
        ));

    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {

        parent::configureFormFields($formMapper);

        $object = $this->getSubject();

        $metadata_manager = $this->getResourceManager()->getMetadataManager();

        $request = $this->getRequest();

        $formMapper
            ->tab('form.tab.datamodel.description')
            ->with('form.group.datamodel.description', array("class" => "col-md-6"))
            ->add('name')
            ->add('title')
            ->add('description')
            ->end()
            ->with('form.group.datamodel.configuration', array("class" => "col-md-6"))
            ->add('metadata', 'entity', array(
                'required' => true,
                'disabled' => $object->getId() != null,
                'class'=>$metadata_manager->getResourceClassShortcut(),
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er) use ($metadata_manager) {
                    /* @var $metadata_manager MetadataManager */
                    $qb = $metadata_manager->createQueryBuilder();
                    $qb->addOrderBy($qb->getRootAlias() . '.enabled', 'DESC');
                    $qb->addOrderBy($qb->getRootAlias() . '.title', 'ASC');
                    return $qb;
                },
                'choice_label' => 'dataSourceTitleMetadataTitleAndEnabled',
                "multiple" => false, "expanded" => false, 'required' => true
            ))
            ->add('enabled')
            ->end()
            ->end();


        if ($object->getId() != null) {

            $formMapper
                ->tab('form.tab.datamodel.details')
                ->with('form.group.datamodel.select_details')
                ->add('details', 'sonata_type_collection', array(
                    "label" => false,
                    "by_reference" => false,
                    'type_options' => array('delete' => true),
                    //'cascade_validation' => true,
                ), array(
                    'edit' => 'inline',
                    'inline' => 'table',
                    //'sortable' => 'position',
                ))
                ->end()
                ->end()
            ;

            $formMapper
                ->tab('form.tab.datamodel.groups')
                ->with('form.group.datamodel.select_groups')
                ->add('groups', 'sonata_type_collection', array(
                    "label" => false,
                    "by_reference" => false,
                    'type_options' => array('delete' => true),
                    //'cascade_validation' => true,
                ), array(
                    'edit' => 'inline',
                    'inline' => 'table',
                    //'sortable' => 'position',
                ))
                ->end()
                ->end()
            ;

            $formMapper
                ->tab('form.tab.datamodel.orders')
                ->with('form.group.datamodel.select_orders')
                ->add('orders', 'sonata_type_collection', array(
                    "label" => false,
                    "by_reference" => false,
                    'type_options' => array('delete' => true),
                    //'cascade_validation' => true,
                ), array(
                    'edit' => 'inline',
                    'inline' => 'table',
                    //'sortable' => 'position',
                ))
                ->end()
                ->end()
            ;

            $formMapper
                ->tab('form.tab.datamodel.conditions')
                ->with('form.group.datamodel.select_conditions')
                ->add('conditions', 'sonata_type_collection', array(
                    "label" => false,
                    "by_reference" => false,
                    'type_options' => array('delete' => true),
                    //'cascade_validation' => true,
                ), array(
                    'edit' => 'inline',
                    'inline' => 'table',
                    //'sortable' => 'position',
                ))
                ->end()
                ->end()
            ;

        }

    }

    /**
     * {@inheritdoc}
     */
    protected function configureSideMenu(\Knp\Menu\ItemInterface $menu, $action, \Sonata\AdminBundle\Admin\AdminInterface $childAdmin = null)
    {

        if (!in_array($action, array('edit'))) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        if (in_array($action, array('edit'))) {
            $menu->addChild(
                $this->trans('link_action_execute'), array('uri' => $admin->generateUrl('execute', array('id' => $id)))
            );
        }
    }

    public function getNewInstance()
    {
        $object = parent::getNewInstance(); // TODO: Change the autogenerated stub
        $object->setEnabled(true);
        return $object;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        parent::validate($errorElement, $object);

        $errorElement
            ->with('name')
            ->assertNotBlank()
            ->assertLength(array('min' => 3))
            ->end()
            ->with('title')
            ->assertNotBlank()
            ->assertLength(array('min' => 3))
            ->end()
            //->with('description')
            //->assertNotBlank()
            //->assertLength(array('min' => 5))
            //->end()
        ;

        if ($object->getId()) {
            $errorElement
                ->with('details')
                ->assertCount(array('min' => 1, 'minMessage' => 'You must indicate at least a DETAIL'))
                ->end();
            if (count($object->getDetails()) > 0) {
                $one_public = false;
                foreach ($object->getDetails() as $d) {
                    if ($d->getEnabled())
                        $one_public = true;
                }

                if (!$one_public) {
                    $errorElement->with('details')->addViolation('You must publicate at least a DETAIL');
                }
            }
        }
    }


}
