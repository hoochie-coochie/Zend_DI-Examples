<?php

namespace MovieApp {
    abstract class AbstractLister {
        public $finder;
        public function __construct(Finder $finder){
            $this->finder = $finder;
        }
    }

    class Lister extends AbstractLister{}

    class Finder {
        public $criteria;
        public function __construct($criteria){
            $this->criteria = $criteria;
        }
    }
}

namespace {
    // bootstrap
    include 'zf2bootstrap' . ((stream_resolve_include_path('zf2bootstrap.php')) ? '.php' : '.dist.php');

    $di = new Zend\Di\Di;

    /* PLEASE READ!
     *
     * The scope of this code is to use configured finder (by title in this case)
     * in all listers extended from AbstractLister
     * 
     */
    $config = new Zend\Di\Configuration(array(
        'instance' => array(
            'alias' => array(
                'title-finder' => 'MovieApp\Finder',
            ),
            'MovieApp\AbstractLister' => array(
                'parameters' => array(
                    // Use configured finder
                    'finder' => 'title-finder'
                ),
            ),
            'title-finder' => array(
                'parameters' => array(
                    'criteria' => 'title'
                ),
            ),
        ),
        'definition' => array(
            'class' => array(
                'MovieApp\AbstractLister' => array(
                    'parameters' => array(
                        'criteria' => array('required' => true),
                    ),
                ),
            ),
        ),
    ));

    $di->configure($config);
    $lister = $di->get('MovieApp\Lister');
    
    // expression to test
    $works = ($lister->finder instanceof MovieApp\Finder && $lister->finder->criteria == 'title');

    // display result
    echo (($works) ? 'It works!' : 'It DOES NOT work!');
}
