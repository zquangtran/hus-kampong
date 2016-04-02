<?php

/*
 * This file is part of the Aseagle package.
 *
 * (c) Quang Tran <quang.tran@aseagle.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Aseagle\Bundle\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityRepository;
use Aseagle\Bundle\AdminBundle\Form\Event\CategorySubscriber;
use Aseagle\Backend\Entity\Category;
use Aseagle\Bundle\AdminBundle\Form\Type\ContentLanguageType;

/**
 * CategoryType
 *
 * @author Quang Tran <quang.tran@aseagle.com>
 */
class CategoryMenuType extends AbstractType {
    
    /**
     * @var ContainerInterface
     */
    protected $container;
    
    protected $type; 

    /**
     * Constructor
     * 
     * @param ContainerInterface $container            
     */
    public function __construct(ContainerInterface $container, $type = 1) {
        $this->container = $container;
        $this->type = $type;
    }

    /*
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('parent', null, array ( 
            'label' => 'Parent Menu',
            'property' => 'propertyName',
            'class' => 'AseagleBackend:Category',
            'empty_value' => "Select...",
            'query_builder' => function(EntityRepository $er) {
                $qBuider = $er->createQueryBuilder('o')                  
                    ->andWhere('o.enabled = 1')
                    ->andWhere("o.type = :type")->setParameter(':type', Category::TYPE_MENU)
                    ->orderBy('o.root, o.lft, o.ordering', 'ASC');                                
                return $qBuider;
            },            
            'attr' => array ( 
                'class' => 'form-control', 
                'placeholder' => 'Category' 
            ) 
        ))->add('ordering', null, array ( 
            'label' => 'Ordering', 
            'attr' => array ( 
                'class' => 'form-control', 
                'placeholder' => 'Ordering' 
            ) 
        ))->add('enabled', 'choice', array ( 
            'label' => 'Status',
            'required' => false, 
            'empty_value' => 'Select...', 
            'choices' => array ( 
                '1' => 'Publish', 
                '0' => 'Un-publish' 
            ), 
            'attr' => array ( 
                'class' => 'form-control' 
            ) 
        ))->add('contentLangs', 'collection', array(
            'type' => new ContentLanguageType(),
            'allow_add'    => true,
            'by_reference' => false,
            'allow_delete' => true,
        ));
        ;
        
        $builder->addEventSubscriber(new CategorySubscriber($this->container, Category::TYPE_MENU));
        
    }

    /*
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\AbstractType::setDefaultOptions()
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array ( 
            'data_class' => 'Aseagle\Backend\Entity\Category', 
        ));
    }

    /*
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\FormTypeInterface::getName()
     */
    public function getName() {
        return 'category';
    }
}
