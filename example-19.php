<?php

namespace MovieApp {
    
    use Doctrine\ORM\EntityManager;

    class Lister implements DoctrineAwareInterface {
        public $em;
        public function setEntityManager(EntityManager $em) {
            $this->em = $em;
        }
    }
    
    interface DoctrineAwareInterface {
        public function setEntityManager(EntityManager $em);
    }
}

namespace {

    use Zend\Di;

    function moduleBootstrap($module, $di) {
        include 'vendor/' . $module . '/Module.php';
        $class = "$module\Module";
        $module = new $class();
        $autoloaderConfig = $module->getAutoloaderConfig();
        $moduleConfig = $module->getConfig();
        Zend\Loader\AutoloaderFactory::factory( $autoloaderConfig );
        $di->configure( new Di\Configuration( $moduleConfig['di'] ) );
    }

    // bootstrap
    include 'zf2bootstrap' . ((stream_resolve_include_path('zf2bootstrap.php')) ? '.php' : '.dist.php');

    $di = new Di\Di;

    // bootstrap DoctrineModule
    moduleBootstrap('DoctrineModule', $di);

    // bootstrap DoctrineORMModule
    moduleBootstrap('DoctrineORMModule', $di);

    // Di\Display\Console::export($di);

    // Use preconfigured Doctrine\ORM\EntityManager
    $im = $di->instanceManager();
    $im->setParameters(
        'MovieApp\DoctrineAware', array( 'em' => 'doctrine_em')
    );

    echo get_class( $di->get('doctrine_em') ); // prints Doctrine\ORM\EntityManager

    // now let's get Doctrine consumer
    $lister = $di->get('MovieApp\Lister'); // BANG! "Invalid instantiator"

    // expression to test
    $works = ($lister->em instanceof Doctrine\ORM\EntityManager);

    // display result
    echo (($works) ? 'It works!' : 'It DOES NOT work!');

}
