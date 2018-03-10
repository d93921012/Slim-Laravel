<?php
namespace LAbasic\Session;

use Carbon\Carbon;
use Illuminate\Session\SessionInterface;
use Illuminate\Session\SessionManager as LaravelSessionManager;
use Slim\Http\Request;
use Slim\Http\Response;

// class Middleware extends \Slim\Middleware
class Service
{
    public static $sessionService = null;
    /**
     * The session manager.
     *
     * @var \Illuminate\Session\SessionManager
     */
    protected $manager;
    protected $ssesion;
    /**
     * Public constructor
     * @param SessionManager $manager
     */
    // public function __construct(SessionManager $manager)
    public function __construct(LaravelSessionManager $manager)
    {
        $this->slimLegacy = class_exists('\Slim\Slim');
        $this->manager = $manager;
        $this->app = $GLOBALS['app'];
    }
    /**
     * Uses Slim's 'slim.before.router' hook to check for user authorization.
     * Will redirect to named login route if user is unauthorized
     *
     * @throws \RuntimeException if there isn't a named 'login' route
     */
    public static function start()
    {
        $app = $GLOBALS['app'];
        
        $la_container = $app->container['la_container'];

        // Filesystem is needed for file based session driver
        // However, it could be initiated somewhere else, like .
        if (! isset($la_container['files'])) {
            $la_container['files'] = function () {
                return new \Illuminate\Filesystem\Filesystem();
            };
        }

        // $container['session'] = new Illuminate\Session\SessionManager($la_container);
        $app->container->singleton('session', function () use ($la_container) {
            return new \Illuminate\Session\SessionManager($la_container);
        });
        $app->container['session.store'] = $app->container['session']->driver();
        // $app->add(new \LAbasic\LaravelSession\Middleware($app->session));
        $sm = new \LAbasic\Session\Service($app->session);
        self::$sessionService = $sm;
        $sm->start_sess();
    }

    public static function stop() 
    {
        if (self::$sessionService != null) {
            self::$sessionService->stop_sess();
        }
    }

    public function start_sess()
    {
        $request = $this->app->request;

        // If a session driver has been configured, we will need to start the session here
        // so that the data is ready for an application. Note that the Laravel sessions
        // do not make use of PHP "native" sessions in any way since they are crappy.
        if ($this->sessionConfigured())
        {
            $this->session = $this->startSession($request);
        }
    }

    public function stop_sess()
    {
        $request = $this->app->request;
        $response = $this->app->response;
        if ($this->sessionConfigured())
        {
            $this->closeSession($this->session);
            $this->addCookieToResponse($response, $this->session);
        }
    }

    /**
     * Start the session for the given request.
     *
     * @param  \Slim\Http\Request  $request
     * @return \Illuminate\Session\SessionInterface
     */
    protected function startSession(Request $request)
    {
        $session = $this->getSession($request);
        $session->start();
        return $session;
    }
    /**
     * Close the session handling for the request.
     *
     * @param  \Illuminate\Session\SessionInterface  $session
     * @return void
     */
    protected function closeSession(SessionInterface $session)
    {
        $session->save();
        $this->collectGarbage($session);
    }
    /**
     * Remove the garbage from the session if necessary.
     *
     * @param  \Illuminate\Session\SessionInterface  $session
     * @return void
     */
    protected function collectGarbage(SessionInterface $session)
    {
        $config = $this->manager->getSessionConfig();
        // Here we will see if this request hits the garbage collection lottery by hitting
        // the odds needed to perform garbage collection on any given request. If we do
        // hit it, we'll call this handler to let it delete all the expired sessions.
        if ($this->configHitsLottery($config))
        {
            $session->getHandler()->gc($this->getLifetimeSeconds());
        }
    }
    /**
     * Determine if the configuration odds hit the lottery.
     *
     * @param  array  $config
     * @return bool
     */
    protected function configHitsLottery(array $config)
    {
        return mt_rand(1, $config['lottery'][1]) <= $config['lottery'][0];
    }
    /**
     * @param \Slim\Http\Response $response
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     */
    protected function addCookieToResponse(Response $response, $session)
    {
        $s = $session;
        if ($this->sessionIsPersistent($c = $this->manager->getSessionConfig()))
        {
            $secure = array_get($c, 'secure', false);
            $data = array(
                'value' => $s->getId(),
                'time' => $this->getCookieLifetime(),
                'path' => $c['path'],
                'domain' => $c['domain'],
                'secure' => $secure
            );
            if ($this->slimLegacy) {
                $response->cookies->set($s->getName(), $data);
            } else {
                $response->setCookie($s->getName(), $data);
            }
        }
    }
    /**
     * Get the session lifetime in seconds.
     *
     *
     */
    protected function getLifetimeSeconds()
    {
        return array_get($this->manager->getSessionConfig(), 'lifetime') * 60;
    }
    /**
     * Get the cookie lifetime in seconds.
     *
     * @return int
     */
    protected function getCookieLifetime()
    {
        $config = $this->manager->getSessionConfig();
        return $config['expire_on_close'] ? 0 : Carbon::now()->addMinutes($config['lifetime']);
    }
    /**
     * Determine if a session driver has been configured.
     *
     * @return bool
     */
    protected function sessionConfigured()
    {
        // return ! is_null(array_get($this->manager->getSessionConfig(), 'driver'));
        return ! is_null(array_get($this->manager->getSessionConfig(), 'driver'));
    }
    /**
     * Determine if the configured session driver is persistent.
     *
     * @param  array|null  $config
     * @return bool
     */
    protected function sessionIsPersistent(array $config = null)
    {
        // Some session drivers are not persistent, such as the test array driver or even
        // when the developer don't have a session driver configured at all, which the
        // session cookies will not need to get set on any responses in those cases.
        $config = $config ?: $this->manager->getSessionConfig();
        return ! in_array($config['driver'], array(null, 'array'));
    }
    /**
     * @param \Slim\Http\Request $request
     * @return \Illuminate\Session\SessionInterface
     */
    private function getSession(Request $request)
    {
        $session = $this->manager->driver();
        $cookieData = $this->slimLegacy ? $request->cookies->get($session->getName()) : $request->getCookie($session->getName());
        $session->setId($cookieData);
        return $session;
    }
}
