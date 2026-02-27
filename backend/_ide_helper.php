<?php

namespace Illuminate\Support\Facades {
    
    class App {
        
        public static function configure($basePath = null)
        {
            return \Illuminate\Foundation\Application::configure($basePath);
        }

        public static function inferBasePath()
        {
            return \Illuminate\Foundation\Application::inferBasePath();
        }

        public static function version()
        {
            
            return $instance->version();
        }

        public static function bootstrapWith($bootstrappers)
        {
            
            $instance->bootstrapWith($bootstrappers);
        }

        public static function afterLoadingEnvironment($callback)
        {
            
            $instance->afterLoadingEnvironment($callback);
        }

        public static function beforeBootstrapping($bootstrapper, $callback)
        {
            
            $instance->beforeBootstrapping($bootstrapper, $callback);
        }

        public static function afterBootstrapping($bootstrapper, $callback)
        {
            
            $instance->afterBootstrapping($bootstrapper, $callback);
        }

        public static function hasBeenBootstrapped()
        {
            
            return $instance->hasBeenBootstrapped();
        }

        public static function setBasePath($basePath)
        {
            
            return $instance->setBasePath($basePath);
        }

        public static function path($path = '')
        {
            
            return $instance->path($path);
        }

        public static function useAppPath($path)
        {
            
            return $instance->useAppPath($path);
        }

        public static function basePath($path = '')
        {
            
            return $instance->basePath($path);
        }

        public static function bootstrapPath($path = '')
        {
            
            return $instance->bootstrapPath($path);
        }

        public static function getBootstrapProvidersPath()
        {
            
            return $instance->getBootstrapProvidersPath();
        }

        public static function useBootstrapPath($path)
        {
            
            return $instance->useBootstrapPath($path);
        }

        public static function configPath($path = '')
        {
            
            return $instance->configPath($path);
        }

        public static function useConfigPath($path)
        {
            
            return $instance->useConfigPath($path);
        }

        public static function databasePath($path = '')
        {
            
            return $instance->databasePath($path);
        }

        public static function useDatabasePath($path)
        {
            
            return $instance->useDatabasePath($path);
        }

        public static function langPath($path = '')
        {
            
            return $instance->langPath($path);
        }

        public static function useLangPath($path)
        {
            
            return $instance->useLangPath($path);
        }

        public static function publicPath($path = '')
        {
            
            return $instance->publicPath($path);
        }

        public static function usePublicPath($path)
        {
            
            return $instance->usePublicPath($path);
        }

        public static function storagePath($path = '')
        {
            
            return $instance->storagePath($path);
        }

        public static function useStoragePath($path)
        {
            
            return $instance->useStoragePath($path);
        }

        public static function resourcePath($path = '')
        {
            
            return $instance->resourcePath($path);
        }

        public static function viewPath($path = '')
        {
            
            return $instance->viewPath($path);
        }

        public static function joinPaths($basePath, $path = '')
        {
            
            return $instance->joinPaths($basePath, $path);
        }

        public static function environmentPath()
        {
            
            return $instance->environmentPath();
        }

        public static function useEnvironmentPath($path)
        {
            
            return $instance->useEnvironmentPath($path);
        }

        public static function loadEnvironmentFrom($file)
        {
            
            return $instance->loadEnvironmentFrom($file);
        }

        public static function environmentFile()
        {
            
            return $instance->environmentFile();
        }

        public static function environmentFilePath()
        {
            
            return $instance->environmentFilePath();
        }

        public static function environment(...$environments)
        {
            
            return $instance->environment(...$environments);
        }

        public static function isLocal()
        {
            
            return $instance->isLocal();
        }

        public static function isProduction()
        {
            
            return $instance->isProduction();
        }

        public static function detectEnvironment($callback)
        {
            
            return $instance->detectEnvironment($callback);
        }

        public static function runningInConsole()
        {
            
            return $instance->runningInConsole();
        }

        public static function runningConsoleCommand(...$commands)
        {
            
            return $instance->runningConsoleCommand(...$commands);
        }

        public static function runningUnitTests()
        {
            
            return $instance->runningUnitTests();
        }

        public static function hasDebugModeEnabled()
        {
            
            return $instance->hasDebugModeEnabled();
        }

        public static function registered($callback)
        {
            
            $instance->registered($callback);
        }

        public static function registerConfiguredProviders()
        {
            
            $instance->registerConfiguredProviders();
        }

        public static function register($provider, $force = false)
        {
            
            return $instance->register($provider, $force);
        }

        public static function getProvider($provider)
        {
            
            return $instance->getProvider($provider);
        }

        public static function getProviders($provider)
        {
            
            return $instance->getProviders($provider);
        }

        public static function resolveProvider($provider)
        {
            
            return $instance->resolveProvider($provider);
        }

        public static function loadDeferredProviders()
        {
            
            $instance->loadDeferredProviders();
        }

        public static function loadDeferredProvider($service)
        {
            
            $instance->loadDeferredProvider($service);
        }

        public static function registerDeferredProvider($provider, $service = null)
        {
            
            $instance->registerDeferredProvider($provider, $service);
        }

        public static function make($abstract, $parameters = [])
        {
            
            return $instance->make($abstract, $parameters);
        }

        public static function bound($abstract)
        {
            
            return $instance->bound($abstract);
        }

        public static function isBooted()
        {
            
            return $instance->isBooted();
        }

        public static function boot()
        {
            
            $instance->boot();
        }

        public static function booting($callback)
        {
            
            $instance->booting($callback);
        }

        public static function booted($callback)
        {
            
            $instance->booted($callback);
        }

        public static function handle($request, $type = 1, $catch = true)
        {
            
            return $instance->handle($request, $type, $catch);
        }

        public static function handleRequest($request)
        {
            
            $instance->handleRequest($request);
        }

        public static function handleCommand($input)
        {
            
            return $instance->handleCommand($input);
        }

        public static function shouldMergeFrameworkConfiguration()
        {
            
            return $instance->shouldMergeFrameworkConfiguration();
        }

        public static function dontMergeFrameworkConfiguration()
        {
            
            return $instance->dontMergeFrameworkConfiguration();
        }

        public static function shouldSkipMiddleware()
        {
            
            return $instance->shouldSkipMiddleware();
        }

        public static function getCachedServicesPath()
        {
            
            return $instance->getCachedServicesPath();
        }

        public static function getCachedPackagesPath()
        {
            
            return $instance->getCachedPackagesPath();
        }

        public static function configurationIsCached()
        {
            
            return $instance->configurationIsCached();
        }

        public static function getCachedConfigPath()
        {
            
            return $instance->getCachedConfigPath();
        }

        public static function routesAreCached()
        {
            
            return $instance->routesAreCached();
        }

        public static function getCachedRoutesPath()
        {
            
            return $instance->getCachedRoutesPath();
        }

        public static function eventsAreCached()
        {
            
            return $instance->eventsAreCached();
        }

        public static function getCachedEventsPath()
        {
            
            return $instance->getCachedEventsPath();
        }

        public static function addAbsoluteCachePathPrefix($prefix)
        {
            
            return $instance->addAbsoluteCachePathPrefix($prefix);
        }

        public static function maintenanceMode()
        {
            
            return $instance->maintenanceMode();
        }

        public static function isDownForMaintenance()
        {
            
            return $instance->isDownForMaintenance();
        }

        public static function abort($code, $message = '', $headers = [])
        {
            
            return $instance->abort($code, $message, $headers);
        }

        public static function terminating($callback)
        {
            
            return $instance->terminating($callback);
        }

        public static function terminate()
        {
            
            $instance->terminate();
        }

        public static function getLoadedProviders()
        {
            
            return $instance->getLoadedProviders();
        }

        public static function providerIsLoaded($provider)
        {
            
            return $instance->providerIsLoaded($provider);
        }

        public static function getDeferredServices()
        {
            
            return $instance->getDeferredServices();
        }

        public static function setDeferredServices($services)
        {
            
            $instance->setDeferredServices($services);
        }

        public static function isDeferredService($service)
        {
            
            return $instance->isDeferredService($service);
        }

        public static function addDeferredServices($services)
        {
            
            $instance->addDeferredServices($services);
        }

        public static function removeDeferredServices($services)
        {
            
            $instance->removeDeferredServices($services);
        }

        public static function provideFacades($namespace)
        {
            
            $instance->provideFacades($namespace);
        }

        public static function getLocale()
        {
            
            return $instance->getLocale();
        }

        public static function currentLocale()
        {
            
            return $instance->currentLocale();
        }

        public static function getFallbackLocale()
        {
            
            return $instance->getFallbackLocale();
        }

        public static function setLocale($locale)
        {
            
            $instance->setLocale($locale);
        }

        public static function setFallbackLocale($fallbackLocale)
        {
            
            $instance->setFallbackLocale($fallbackLocale);
        }

        public static function isLocale($locale)
        {
            
            return $instance->isLocale($locale);
        }

        public static function registerCoreContainerAliases()
        {
            
            $instance->registerCoreContainerAliases();
        }

        public static function flush()
        {
            
            $instance->flush();
        }

        public static function getNamespace()
        {
            
            return $instance->getNamespace();
        }

        public static function when($concrete)
        {

            return $instance->when($concrete);
        }

        public static function whenHasAttribute($attribute, $handler)
        {

            $instance->whenHasAttribute($attribute, $handler);
        }

        public static function has($id)
        {

            return $instance->has($id);
        }

        public static function resolved($abstract)
        {

            return $instance->resolved($abstract);
        }

        public static function isShared($abstract)
        {

            return $instance->isShared($abstract);
        }

        public static function isAlias($name)
        {

            return $instance->isAlias($name);
        }

        public static function bind($abstract, $concrete = null, $shared = false)
        {

            $instance->bind($abstract, $concrete, $shared);
        }

        public static function hasMethodBinding($method)
        {

            return $instance->hasMethodBinding($method);
        }

        public static function bindMethod($method, $callback)
        {

            $instance->bindMethod($method, $callback);
        }

        public static function callMethodBinding($method, $instance)
        {

            return $instance->callMethodBinding($method, $instance);
        }

        public static function addContextualBinding($concrete, $abstract, $implementation)
        {

            $instance->addContextualBinding($concrete, $abstract, $implementation);
        }

        public static function bindIf($abstract, $concrete = null, $shared = false)
        {

            $instance->bindIf($abstract, $concrete, $shared);
        }

        public static function singleton($abstract, $concrete = null)
        {

            $instance->singleton($abstract, $concrete);
        }

        public static function singletonIf($abstract, $concrete = null)
        {

            $instance->singletonIf($abstract, $concrete);
        }

        public static function scoped($abstract, $concrete = null)
        {

            $instance->scoped($abstract, $concrete);
        }

        public static function scopedIf($abstract, $concrete = null)
        {

            $instance->scopedIf($abstract, $concrete);
        }

        public static function extend($abstract, $closure)
        {

            $instance->extend($abstract, $closure);
        }

        public static function instance($abstract, $instance)
        {

            return $instance->instance($abstract, $instance);
        }

        public static function tag($abstracts, $tags)
        {

            $instance->tag($abstracts, $tags);
        }

        public static function tagged($tag)
        {

            return $instance->tagged($tag);
        }

        public static function alias($abstract, $alias)
        {

            $instance->alias($abstract, $alias);
        }

        public static function rebinding($abstract, $callback)
        {

            return $instance->rebinding($abstract, $callback);
        }

        public static function refresh($abstract, $target, $method)
        {

            return $instance->refresh($abstract, $target, $method);
        }

        public static function wrap($callback, $parameters = [])
        {

            return $instance->wrap($callback, $parameters);
        }

        public static function call($callback, $parameters = [], $defaultMethod = null)
        {

            return $instance->call($callback, $parameters, $defaultMethod);
        }

        public static function factory($abstract)
        {

            return $instance->factory($abstract);
        }

        public static function makeWith($abstract, $parameters = [])
        {

            return $instance->makeWith($abstract, $parameters);
        }

        public static function get($id)
        {

            return $instance->get($id);
        }

        public static function build($concrete)
        {

            return $instance->build($concrete);
        }

        public static function resolveFromAttribute($attribute)
        {

            return $instance->resolveFromAttribute($attribute);
        }

        public static function beforeResolving($abstract, $callback = null)
        {

            $instance->beforeResolving($abstract, $callback);
        }

        public static function resolving($abstract, $callback = null)
        {

            $instance->resolving($abstract, $callback);
        }

        public static function afterResolving($abstract, $callback = null)
        {

            $instance->afterResolving($abstract, $callback);
        }

        public static function afterResolvingAttribute($attribute, $callback)
        {

            $instance->afterResolvingAttribute($attribute, $callback);
        }

        public static function fireAfterResolvingAttributeCallbacks($attributes, $object)
        {

            $instance->fireAfterResolvingAttributeCallbacks($attributes, $object);
        }

        public static function currentlyResolving()
        {

            return $instance->currentlyResolving();
        }

        public static function getBindings()
        {

            return $instance->getBindings();
        }

        public static function getAlias($abstract)
        {

            return $instance->getAlias($abstract);
        }

        public static function forgetExtenders($abstract)
        {

            $instance->forgetExtenders($abstract);
        }

        public static function forgetInstance($abstract)
        {

            $instance->forgetInstance($abstract);
        }

        public static function forgetInstances()
        {

            $instance->forgetInstances();
        }

        public static function forgetScopedInstances()
        {

            $instance->forgetScopedInstances();
        }

        public static function resolveEnvironmentUsing($callback)
        {

            $instance->resolveEnvironmentUsing($callback);
        }

        public static function currentEnvironmentIs($environments)
        {

            return $instance->currentEnvironmentIs($environments);
        }

        public static function getInstance()
        {
            
            return \Illuminate\Foundation\Application::getInstance();
        }

        public static function setInstance($container = null)
        {
            
            return \Illuminate\Foundation\Application::setInstance($container);
        }

        public static function offsetExists($key)
        {

            return $instance->offsetExists($key);
        }

        public static function offsetGet($key)
        {

            return $instance->offsetGet($key);
        }

        public static function offsetSet($key, $value)
        {

            return $instance->offsetSet($key, $value);
        }

        public static function offsetUnset($key)
        {

            return $instance->offsetUnset($key);
        }

        public static function macro($name, $macro)
        {
            \Illuminate\Foundation\Application::macro($name, $macro);
        }

        public static function mixin($mixin, $replace = true)
        {
            \Illuminate\Foundation\Application::mixin($mixin, $replace);
        }

        public static function hasMacro($name)
        {
            return \Illuminate\Foundation\Application::hasMacro($name);
        }

        public static function flushMacros()
        {
            \Illuminate\Foundation\Application::flushMacros();
        }

            }
    
    class Artisan {
        
        public static function rerouteSymfonyCommandEvents()
        {
            
            return $instance->rerouteSymfonyCommandEvents();
        }

        public static function handle($input, $output = null)
        {
            
            return $instance->handle($input, $output);
        }

        public static function terminate($input, $status)
        {
            
            $instance->terminate($input, $status);
        }

        public static function whenCommandLifecycleIsLongerThan($threshold, $handler)
        {
            
            $instance->whenCommandLifecycleIsLongerThan($threshold, $handler);
        }

        public static function commandStartedAt()
        {
            
            return $instance->commandStartedAt();
        }

        public static function resolveConsoleSchedule()
        {
            
            return $instance->resolveConsoleSchedule();
        }

        public static function command($signature, $callback)
        {
            
            return $instance->command($signature, $callback);
        }

        public static function registerCommand($command)
        {
            
            $instance->registerCommand($command);
        }

        public static function call($command, $parameters = [], $outputBuffer = null)
        {
            
            return $instance->call($command, $parameters, $outputBuffer);
        }

        public static function queue($command, $parameters = [])
        {
            
            return $instance->queue($command, $parameters);
        }

        public static function all()
        {
            
            return $instance->all();
        }

        public static function output()
        {
            
            return $instance->output();
        }

        public static function bootstrap()
        {
            
            $instance->bootstrap();
        }

        public static function bootstrapWithoutBootingProviders()
        {
            
            $instance->bootstrapWithoutBootingProviders();
        }

        public static function setArtisan($artisan)
        {
            
            $instance->setArtisan($artisan);
        }

        public static function addCommands($commands)
        {
            
            return $instance->addCommands($commands);
        }

        public static function addCommandPaths($paths)
        {
            
            return $instance->addCommandPaths($paths);
        }

        public static function addCommandRoutePaths($paths)
        {
            
            return $instance->addCommandRoutePaths($paths);
        }

            }
    
    class Auth {
        
        public static function guard($name = null)
        {
            
            return $instance->guard($name);
        }

        public static function createSessionDriver($name, $config)
        {
            
            return $instance->createSessionDriver($name, $config);
        }

        public static function createTokenDriver($name, $config)
        {
            
            return $instance->createTokenDriver($name, $config);
        }

        public static function getDefaultDriver()
        {
            
            return $instance->getDefaultDriver();
        }

        public static function shouldUse($name)
        {
            
            $instance->shouldUse($name);
        }

        public static function setDefaultDriver($name)
        {
            
            $instance->setDefaultDriver($name);
        }

        public static function viaRequest($driver, $callback)
        {
            
            return $instance->viaRequest($driver, $callback);
        }

        public static function userResolver()
        {
            
            return $instance->userResolver();
        }

        public static function resolveUsersUsing($userResolver)
        {
            
            return $instance->resolveUsersUsing($userResolver);
        }

        public static function extend($driver, $callback)
        {
            
            return $instance->extend($driver, $callback);
        }

        public static function provider($name, $callback)
        {
            
            return $instance->provider($name, $callback);
        }

        public static function hasResolvedGuards()
        {
            
            return $instance->hasResolvedGuards();
        }

        public static function forgetGuards()
        {
            
            return $instance->forgetGuards();
        }

        public static function setApplication($app)
        {
            
            return $instance->setApplication($app);
        }

        public static function createUserProvider($provider = null)
        {
            
            return $instance->createUserProvider($provider);
        }

        public static function getDefaultUserProvider()
        {
            
            return $instance->getDefaultUserProvider();
        }

        public static function user()
        {
            
            return $instance->user();
        }

        public static function id()
        {
            
            return $instance->id();
        }

        public static function once($credentials = [])
        {
            
            return $instance->once($credentials);
        }

        public static function onceUsingId($id)
        {
            
            return $instance->onceUsingId($id);
        }

        public static function validate($credentials = [])
        {
            
            return $instance->validate($credentials);
        }

        public static function basic($field = 'email', $extraConditions = [])
        {
            
            return $instance->basic($field, $extraConditions);
        }

        public static function onceBasic($field = 'email', $extraConditions = [])
        {
            
            return $instance->onceBasic($field, $extraConditions);
        }

        public static function attempt($credentials = [], $remember = false)
        {
            
            return $instance->attempt($credentials, $remember);
        }

        public static function attemptWhen($credentials = [], $callbacks = null, $remember = false)
        {
            
            return $instance->attemptWhen($credentials, $callbacks, $remember);
        }

        public static function loginUsingId($id, $remember = false)
        {
            
            return $instance->loginUsingId($id, $remember);
        }

        public static function login($user, $remember = false)
        {
            
            $instance->login($user, $remember);
        }

        public static function hashPasswordForCookie($passwordHash)
        {
            
            return $instance->hashPasswordForCookie($passwordHash);
        }

        public static function logout()
        {
            
            $instance->logout();
        }

        public static function logoutCurrentDevice()
        {
            
            $instance->logoutCurrentDevice();
        }

        public static function logoutOtherDevices($password)
        {
            
            return $instance->logoutOtherDevices($password);
        }

        public static function attempting($callback)
        {
            
            $instance->attempting($callback);
        }

        public static function getLastAttempted()
        {
            
            return $instance->getLastAttempted();
        }

        public static function getName()
        {
            
            return $instance->getName();
        }

        public static function getRecallerName()
        {
            
            return $instance->getRecallerName();
        }

        public static function viaRemember()
        {
            
            return $instance->viaRemember();
        }

        public static function setRememberDuration($minutes)
        {
            
            return $instance->setRememberDuration($minutes);
        }

        public static function getCookieJar()
        {
            
            return $instance->getCookieJar();
        }

        public static function setCookieJar($cookie)
        {
            
            $instance->setCookieJar($cookie);
        }

        public static function getDispatcher()
        {
            
            return $instance->getDispatcher();
        }

        public static function setDispatcher($events)
        {
            
            $instance->setDispatcher($events);
        }

        public static function getSession()
        {
            
            return $instance->getSession();
        }

        public static function getUser()
        {
            
            return $instance->getUser();
        }

        public static function setUser($user)
        {
            
            return $instance->setUser($user);
        }

        public static function getRequest()
        {
            
            return $instance->getRequest();
        }

        public static function setRequest($request)
        {
            
            return $instance->setRequest($request);
        }

        public static function getTimebox()
        {
            
            return $instance->getTimebox();
        }

        public static function authenticate()
        {
            
            return $instance->authenticate();
        }

        public static function hasUser()
        {
            
            return $instance->hasUser();
        }

        public static function check()
        {
            
            return $instance->check();
        }

        public static function guest()
        {
            
            return $instance->guest();
        }

        public static function forgetUser()
        {
            
            return $instance->forgetUser();
        }

        public static function getProvider()
        {
            
            return $instance->getProvider();
        }

        public static function setProvider($provider)
        {
            
            $instance->setProvider($provider);
        }

        public static function macro($name, $macro)
        {
            \Illuminate\Auth\SessionGuard::macro($name, $macro);
        }

        public static function mixin($mixin, $replace = true)
        {
            \Illuminate\Auth\SessionGuard::mixin($mixin, $replace);
        }

        public static function hasMacro($name)
        {
            return \Illuminate\Auth\SessionGuard::hasMacro($name);
        }

        public static function flushMacros()
        {
            \Illuminate\Auth\SessionGuard::flushMacros();
        }

            }
    
    class Blade {
        
        public static function compile($path = null)
        {
            
            $instance->compile($path);
        }

        public static function getPath()
        {
            
            return $instance->getPath();
        }

        public static function setPath($path)
        {
            
            $instance->setPath($path);
        }

        public static function compileString($value)
        {
            
            return $instance->compileString($value);
        }

        public static function render($string, $data = [], $deleteCachedView = false)
        {
            return \Illuminate\View\Compilers\BladeCompiler::render($string, $data, $deleteCachedView);
        }

        public static function renderComponent($component)
        {
            return \Illuminate\View\Compilers\BladeCompiler::renderComponent($component);
        }

        public static function stripParentheses($expression)
        {
            
            return $instance->stripParentheses($expression);
        }

        public static function extend($compiler)
        {
            
            $instance->extend($compiler);
        }

        public static function getExtensions()
        {
            
            return $instance->getExtensions();
        }

        public static function if($name, $callback)
        {
            
            $instance->if($name, $callback);
        }

        public static function check($name, ...$parameters)
        {
            
            return $instance->check($name, ...$parameters);
        }

        public static function component($class, $alias = null, $prefix = '')
        {
            
            $instance->component($class, $alias, $prefix);
        }

        public static function components($components, $prefix = '')
        {
            
            $instance->components($components, $prefix);
        }

        public static function getClassComponentAliases()
        {
            
            return $instance->getClassComponentAliases();
        }

        public static function anonymousComponentPath($path, $prefix = null)
        {
            
            $instance->anonymousComponentPath($path, $prefix);
        }

        public static function anonymousComponentNamespace($directory, $prefix = null)
        {
            
            $instance->anonymousComponentNamespace($directory, $prefix);
        }

        public static function componentNamespace($namespace, $prefix)
        {
            
            $instance->componentNamespace($namespace, $prefix);
        }

        public static function getAnonymousComponentPaths()
        {
            
            return $instance->getAnonymousComponentPaths();
        }

        public static function getAnonymousComponentNamespaces()
        {
            
            return $instance->getAnonymousComponentNamespaces();
        }

        public static function getClassComponentNamespaces()
        {
            
            return $instance->getClassComponentNamespaces();
        }

        public static function aliasComponent($path, $alias = null)
        {
            
            $instance->aliasComponent($path, $alias);
        }

        public static function include($path, $alias = null)
        {
            
            $instance->include($path, $alias);
        }

        public static function aliasInclude($path, $alias = null)
        {
            
            $instance->aliasInclude($path, $alias);
        }

        public static function bindDirective($name, $handler)
        {
            
            $instance->bindDirective($name, $handler);
        }

        public static function directive($name, $handler, $bind = false)
        {
            
            $instance->directive($name, $handler, $bind);
        }

        public static function getCustomDirectives()
        {
            
            return $instance->getCustomDirectives();
        }

        public static function prepareStringsForCompilationUsing($callback)
        {
            
            return $instance->prepareStringsForCompilationUsing($callback);
        }

        public static function precompiler($precompiler)
        {
            
            $instance->precompiler($precompiler);
        }

        public static function usingEchoFormat($format, $callback)
        {
            
            return $instance->usingEchoFormat($format, $callback);
        }

        public static function setEchoFormat($format)
        {
            
            $instance->setEchoFormat($format);
        }

        public static function withDoubleEncoding()
        {
            
            $instance->withDoubleEncoding();
        }

        public static function withoutDoubleEncoding()
        {
            
            $instance->withoutDoubleEncoding();
        }

        public static function withoutComponentTags()
        {
            
            $instance->withoutComponentTags();
        }

        public static function getCompiledPath($path)
        {

            return $instance->getCompiledPath($path);
        }

        public static function isExpired($path)
        {

            return $instance->isExpired($path);
        }

        public static function newComponentHash($component)
        {
            return \Illuminate\View\Compilers\BladeCompiler::newComponentHash($component);
        }

        public static function compileClassComponentOpening($component, $alias, $data, $hash)
        {
            return \Illuminate\View\Compilers\BladeCompiler::compileClassComponentOpening($component, $alias, $data, $hash);
        }

        public static function compileEndComponentClass()
        {
            
            return $instance->compileEndComponentClass();
        }

        public static function sanitizeComponentAttribute($value)
        {
            return \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($value);
        }

        public static function compileEndOnce()
        {
            
            return $instance->compileEndOnce();
        }

        public static function stringable($class, $handler = null)
        {
            
            $instance->stringable($class, $handler);
        }

        public static function compileEchos($value)
        {
            
            return $instance->compileEchos($value);
        }

        public static function applyEchoHandler($value)
        {
            
            return $instance->applyEchoHandler($value);
        }

            }
    
    class Broadcast {
        
        public static function routes($attributes = null)
        {
            
            $instance->routes($attributes);
        }

        public static function userRoutes($attributes = null)
        {
            
            $instance->userRoutes($attributes);
        }

        public static function channelRoutes($attributes = null)
        {
            
            $instance->channelRoutes($attributes);
        }

        public static function socket($request = null)
        {
            
            return $instance->socket($request);
        }

        public static function on($channels)
        {
            
            return $instance->on($channels);
        }

        public static function private($channel)
        {
            
            return $instance->private($channel);
        }

        public static function presence($channel)
        {
            
            return $instance->presence($channel);
        }

        public static function event($event = null)
        {
            
            return $instance->event($event);
        }

        public static function queue($event)
        {
            
            $instance->queue($event);
        }

        public static function connection($driver = null)
        {
            
            return $instance->connection($driver);
        }

        public static function driver($name = null)
        {
            
            return $instance->driver($name);
        }

        public static function pusher($config)
        {
            
            return $instance->pusher($config);
        }

        public static function ably($config)
        {
            
            return $instance->ably($config);
        }

        public static function getDefaultDriver()
        {
            
            return $instance->getDefaultDriver();
        }

        public static function setDefaultDriver($name)
        {
            
            $instance->setDefaultDriver($name);
        }

        public static function purge($name = null)
        {
            
            $instance->purge($name);
        }

        public static function extend($driver, $callback)
        {
            
            return $instance->extend($driver, $callback);
        }

        public static function getApplication()
        {
            
            return $instance->getApplication();
        }

        public static function setApplication($app)
        {
            
            return $instance->setApplication($app);
        }

        public static function forgetDrivers()
        {
            
            return $instance->forgetDrivers();
        }

            }
    
    class Bus {
        
        public static function dispatch($command)
        {
            
            return $instance->dispatch($command);
        }

        public static function dispatchSync($command, $handler = null)
        {
            
            return $instance->dispatchSync($command, $handler);
        }

        public static function dispatchNow($command, $handler = null)
        {
            
            return $instance->dispatchNow($command, $handler);
        }

        public static function findBatch($batchId)
        {
            
            return $instance->findBatch($batchId);
        }

        public static function batch($jobs)
        {
            
            return $instance->batch($jobs);
        }

        public static function chain($jobs = null)
        {
            
            return $instance->chain($jobs);
        }

        public static function hasCommandHandler($command)
        {
            
            return $instance->hasCommandHandler($command);
        }

        public static function getCommandHandler($command)
        {
            
            return $instance->getCommandHandler($command);
        }

        public static function dispatchToQueue($command)
        {
            
            return $instance->dispatchToQueue($command);
        }

        public static function dispatchAfterResponse($command, $handler = null)
        {
            
            $instance->dispatchAfterResponse($command, $handler);
        }

        public static function pipeThrough($pipes)
        {
            
            return $instance->pipeThrough($pipes);
        }

        public static function map($map)
        {
            
            return $instance->map($map);
        }

        public static function withDispatchingAfterResponses()
        {
            
            return $instance->withDispatchingAfterResponses();
        }

        public static function withoutDispatchingAfterResponses()
        {
            
            return $instance->withoutDispatchingAfterResponses();
        }

        public static function except($jobsToDispatch)
        {
            
            return $instance->except($jobsToDispatch);
        }

        public static function assertDispatched($command, $callback = null)
        {
            
            $instance->assertDispatched($command, $callback);
        }

        public static function assertDispatchedOnce($command)
        {
            
            $instance->assertDispatchedOnce($command);
        }

        public static function assertDispatchedTimes($command, $times = 1)
        {
            
            $instance->assertDispatchedTimes($command, $times);
        }

        public static function assertNotDispatched($command, $callback = null)
        {
            
            $instance->assertNotDispatched($command, $callback);
        }

        public static function assertNothingDispatched()
        {
            
            $instance->assertNothingDispatched();
        }

        public static function assertDispatchedSync($command, $callback = null)
        {
            
            $instance->assertDispatchedSync($command, $callback);
        }

        public static function assertDispatchedSyncTimes($command, $times = 1)
        {
            
            $instance->assertDispatchedSyncTimes($command, $times);
        }

        public static function assertNotDispatchedSync($command, $callback = null)
        {
            
            $instance->assertNotDispatchedSync($command, $callback);
        }

        public static function assertDispatchedAfterResponse($command, $callback = null)
        {
            
            $instance->assertDispatchedAfterResponse($command, $callback);
        }

        public static function assertDispatchedAfterResponseTimes($command, $times = 1)
        {
            
            $instance->assertDispatchedAfterResponseTimes($command, $times);
        }

        public static function assertNotDispatchedAfterResponse($command, $callback = null)
        {
            
            $instance->assertNotDispatchedAfterResponse($command, $callback);
        }

        public static function assertChained($expectedChain)
        {
            
            $instance->assertChained($expectedChain);
        }

        public static function assertNothingChained()
        {
            
            $instance->assertNothingChained();
        }

        public static function assertDispatchedWithoutChain($command, $callback = null)
        {
            
            $instance->assertDispatchedWithoutChain($command, $callback);
        }

        public static function chainedBatch($callback)
        {
            
            return $instance->chainedBatch($callback);
        }

        public static function assertBatched($callback)
        {
            
            $instance->assertBatched($callback);
        }

        public static function assertBatchCount($count)
        {
            
            $instance->assertBatchCount($count);
        }

        public static function assertNothingBatched()
        {
            
            $instance->assertNothingBatched();
        }

        public static function assertNothingPlaced()
        {
            
            $instance->assertNothingPlaced();
        }

        public static function dispatched($command, $callback = null)
        {
            
            return $instance->dispatched($command, $callback);
        }

        public static function dispatchedSync($command, $callback = null)
        {
            
            return $instance->dispatchedSync($command, $callback);
        }

        public static function dispatchedAfterResponse($command, $callback = null)
        {
            
            return $instance->dispatchedAfterResponse($command, $callback);
        }

        public static function batched($callback)
        {
            
            return $instance->batched($callback);
        }

        public static function hasDispatched($command)
        {
            
            return $instance->hasDispatched($command);
        }

        public static function hasDispatchedSync($command)
        {
            
            return $instance->hasDispatchedSync($command);
        }

        public static function hasDispatchedAfterResponse($command)
        {
            
            return $instance->hasDispatchedAfterResponse($command);
        }

        public static function dispatchFakeBatch($name = '')
        {
            
            return $instance->dispatchFakeBatch($name);
        }

        public static function recordPendingBatch($pendingBatch)
        {
            
            return $instance->recordPendingBatch($pendingBatch);
        }

        public static function serializeAndRestore($serializeAndRestore = true)
        {
            
            return $instance->serializeAndRestore($serializeAndRestore);
        }

        public static function dispatchedBatches()
        {
            
            return $instance->dispatchedBatches();
        }

            }
    
    class Cache {
        
        public static function store($name = null)
        {
            
            return $instance->store($name);
        }

        public static function driver($driver = null)
        {
            
            return $instance->driver($driver);
        }

        public static function memo($driver = null)
        {
            
            return $instance->memo($driver);
        }

        public static function resolve($name)
        {
            
            return $instance->resolve($name);
        }

        public static function build($config)
        {
            
            return $instance->build($config);
        }

        public static function repository($store, $config = [])
        {
            
            return $instance->repository($store, $config);
        }

        public static function refreshEventDispatcher()
        {
            
            $instance->refreshEventDispatcher();
        }

        public static function getDefaultDriver()
        {
            
            return $instance->getDefaultDriver();
        }

        public static function setDefaultDriver($name)
        {
            
            $instance->setDefaultDriver($name);
        }

        public static function forgetDriver($name = null)
        {
            
            return $instance->forgetDriver($name);
        }

        public static function purge($name = null)
        {
            
            $instance->purge($name);
        }

        public static function extend($driver, $callback)
        {
            
            return $instance->extend($driver, $callback);
        }

        public static function setApplication($app)
        {
            
            return $instance->setApplication($app);
        }

        public static function has($key)
        {
            
            return $instance->has($key);
        }

        public static function missing($key)
        {
            
            return $instance->missing($key);
        }

        public static function get($key, $default = null)
        {
            
            return $instance->get($key, $default);
        }

        public static function many($keys)
        {
            
            return $instance->many($keys);
        }

        public static function getMultiple($keys, $default = null)
        {
            
            return $instance->getMultiple($keys, $default);
        }

        public static function pull($key, $default = null)
        {
            
            return $instance->pull($key, $default);
        }

        public static function put($key, $value, $ttl = null)
        {
            
            return $instance->put($key, $value, $ttl);
        }

        public static function set($key, $value, $ttl = null)
        {
            
            return $instance->set($key, $value, $ttl);
        }

        public static function putMany($values, $ttl = null)
        {
            
            return $instance->putMany($values, $ttl);
        }

        public static function setMultiple($values, $ttl = null)
        {
            
            return $instance->setMultiple($values, $ttl);
        }

        public static function add($key, $value, $ttl = null)
        {
            
            return $instance->add($key, $value, $ttl);
        }

        public static function increment($key, $value = 1)
        {
            
            return $instance->increment($key, $value);
        }

        public static function decrement($key, $value = 1)
        {
            
            return $instance->decrement($key, $value);
        }

        public static function forever($key, $value)
        {
            
            return $instance->forever($key, $value);
        }

        public static function remember($key, $ttl, $callback)
        {
            
            return $instance->remember($key, $ttl, $callback);
        }

        public static function sear($key, $callback)
        {
            
            return $instance->sear($key, $callback);
        }

        public static function rememberForever($key, $callback)
        {
            
            return $instance->rememberForever($key, $callback);
        }

        public static function flexible($key, $ttl, $callback, $lock = null, $alwaysDefer = false)
        {
            
            return $instance->flexible($key, $ttl, $callback, $lock, $alwaysDefer);
        }

        public static function withoutOverlapping($key, $callback, $lockFor = 0, $waitFor = 10, $owner = null)
        {
            
            return $instance->withoutOverlapping($key, $callback, $lockFor, $waitFor, $owner);
        }

        public static function forget($key)
        {
            
            return $instance->forget($key);
        }

        public static function delete($key)
        {
            
            return $instance->delete($key);
        }

        public static function deleteMultiple($keys)
        {
            
            return $instance->deleteMultiple($keys);
        }

        public static function clear()
        {
            
            return $instance->clear();
        }

        public static function tags($names)
        {
            
            return $instance->tags($names);
        }

        public static function getName()
        {
            
            return $instance->getName();
        }

        public static function supportsTags()
        {
            
            return $instance->supportsTags();
        }

        public static function getDefaultCacheTime()
        {
            
            return $instance->getDefaultCacheTime();
        }

        public static function setDefaultCacheTime($seconds)
        {
            
            return $instance->setDefaultCacheTime($seconds);
        }

        public static function getStore()
        {
            
            return $instance->getStore();
        }

        public static function setStore($store)
        {
            
            return $instance->setStore($store);
        }

        public static function getEventDispatcher()
        {
            
            return $instance->getEventDispatcher();
        }

        public static function setEventDispatcher($events)
        {
            
            $instance->setEventDispatcher($events);
        }

        public static function offsetExists($key)
        {
            
            return $instance->offsetExists($key);
        }

        public static function offsetGet($key)
        {
            
            return $instance->offsetGet($key);
        }

        public static function offsetSet($key, $value)
        {
            
            $instance->offsetSet($key, $value);
        }

        public static function offsetUnset($key)
        {
            
            $instance->offsetUnset($key);
        }

        public static function macro($name, $macro)
        {
            \Illuminate\Cache\Repository::macro($name, $macro);
        }

        public static function mixin($mixin, $replace = true)
        {
            \Illuminate\Cache\Repository::mixin($mixin, $replace);
        }

        public static function hasMacro($name)
        {
            return \Illuminate\Cache\Repository::hasMacro($name);
        }

        public static function flushMacros()
        {
            \Illuminate\Cache\Repository::flushMacros();
        }

        public static function macroCall($method, $parameters)
        {
            
            return $instance->macroCall($method, $parameters);
        }

        public static function lock($name, $seconds = 0, $owner = null)
        {
            
            return $instance->lock($name, $seconds, $owner);
        }

        public static function restoreLock($name, $owner)
        {
            
            return $instance->restoreLock($name, $owner);
        }

        public static function forgetIfExpired($key)
        {
            
            return $instance->forgetIfExpired($key);
        }

        public static function flush()
        {
            
            return $instance->flush();
        }

        public static function getConnection()
        {
            
            return $instance->getConnection();
        }

        public static function setConnection($connection)
        {
            
            return $instance->setConnection($connection);
        }

        public static function getLockConnection()
        {
            
            return $instance->getLockConnection();
        }

        public static function setLockConnection($connection)
        {
            
            return $instance->setLockConnection($connection);
        }

        public static function getPrefix()
        {
            
            return $instance->getPrefix();
        }

        public static function setPrefix($prefix)
        {
            
            $instance->setPrefix($prefix);
        }

            }
    
    class Concurrency {
        
        public static function driver($name = null)
        {
            
            return $instance->driver($name);
        }

        public static function createProcessDriver($config)
        {
            
            return $instance->createProcessDriver($config);
        }

        public static function createForkDriver($config)
        {
            
            return $instance->createForkDriver($config);
        }

        public static function createSyncDriver($config)
        {
            
            return $instance->createSyncDriver($config);
        }

        public static function getDefaultInstance()
        {
            
            return $instance->getDefaultInstance();
        }

        public static function setDefaultInstance($name)
        {
            
            $instance->setDefaultInstance($name);
        }

        public static function getInstanceConfig($name)
        {
            
            return $instance->getInstanceConfig($name);
        }

        public static function instance($name = null)
        {

            return $instance->instance($name);
        }

        public static function forgetInstance($name = null)
        {

            return $instance->forgetInstance($name);
        }

        public static function purge($name = null)
        {

            $instance->purge($name);
        }

        public static function extend($name, $callback)
        {

            return $instance->extend($name, $callback);
        }

        public static function setApplication($app)
        {

            return $instance->setApplication($app);
        }

            }
    
    class Config {
        
        public static function has($key)
        {
            
            return $instance->has($key);
        }

        public static function get($key, $default = null)
        {
            
            return $instance->get($key, $default);
        }

        public static function getMany($keys)
        {
            
            return $instance->getMany($keys);
        }

        public static function string($key, $default = null)
        {
            
            return $instance->string($key, $default);
        }

        public static function integer($key, $default = null)
        {
            
            return $instance->integer($key, $default);
        }

        public static function float($key, $default = null)
        {
            
            return $instance->float($key, $default);
        }

        public static function boolean($key, $default = null)
        {
            
            return $instance->boolean($key, $default);
        }

        public static function array($key, $default = null)
        {
            
            return $instance->array($key, $default);
        }

        public static function collection($key, $default = null)
        {
            
            return $instance->collection($key, $default);
        }

        public static function set($key, $value = null)
        {
            
            $instance->set($key, $value);
        }

        public static function prepend($key, $value)
        {
            
            $instance->prepend($key, $value);
        }

        public static function push($key, $value)
        {
            
            $instance->push($key, $value);
        }

        public static function all()
        {
            
            return $instance->all();
        }

        public static function offsetExists($key)
        {
            
            return $instance->offsetExists($key);
        }

        public static function offsetGet($key)
        {
            
            return $instance->offsetGet($key);
        }

        public static function offsetSet($key, $value)
        {
            
            $instance->offsetSet($key, $value);
        }

        public static function offsetUnset($key)
        {
            
            $instance->offsetUnset($key);
        }

        public static function macro($name, $macro)
        {
            \Illuminate\Config\Repository::macro($name, $macro);
        }

        public static function mixin($mixin, $replace = true)
        {
            \Illuminate\Config\Repository::mixin($mixin, $replace);
        }

        public static function hasMacro($name)
        {
            return \Illuminate\Config\Repository::hasMacro($name);
        }

        public static function flushMacros()
        {
            \Illuminate\Config\Repository::flushMacros();
        }

            }
    
    class Context {
        
        public static function has($key)
        {
            
            return $instance->has($key);
        }

        public static function missing($key)
        {
            
            return $instance->missing($key);
        }

        public static function hasHidden($key)
        {
            
            return $instance->hasHidden($key);
        }

        public static function missingHidden($key)
        {
            
            return $instance->missingHidden($key);
        }

        public static function all()
        {
            
            return $instance->all();
        }

        public static function allHidden()
        {
            
            return $instance->allHidden();
        }

        public static function get($key, $default = null)
        {
            
            return $instance->get($key, $default);
        }

        public static function getHidden($key, $default = null)
        {
            
            return $instance->getHidden($key, $default);
        }

        public static function pull($key, $default = null)
        {
            
            return $instance->pull($key, $default);
        }

        public static function pullHidden($key, $default = null)
        {
            
            return $instance->pullHidden($key, $default);
        }

        public static function only($keys)
        {
            
            return $instance->only($keys);
        }

        public static function onlyHidden($keys)
        {
            
            return $instance->onlyHidden($keys);
        }

        public static function except($keys)
        {
            
            return $instance->except($keys);
        }

        public static function exceptHidden($keys)
        {
            
            return $instance->exceptHidden($keys);
        }

        public static function add($key, $value = null)
        {
            
            return $instance->add($key, $value);
        }

        public static function addHidden($key, $value = null)
        {
            
            return $instance->addHidden($key, $value);
        }

        public static function remember($key, $value)
        {
            
            return $instance->remember($key, $value);
        }

        public static function rememberHidden($key, $value)
        {
            
            return $instance->rememberHidden($key, $value);
        }

        public static function forget($key)
        {
            
            return $instance->forget($key);
        }

        public static function forgetHidden($key)
        {
            
            return $instance->forgetHidden($key);
        }

        public static function addIf($key, $value)
        {
            
            return $instance->addIf($key, $value);
        }

        public static function addHiddenIf($key, $value)
        {
            
            return $instance->addHiddenIf($key, $value);
        }

        public static function push($key, ...$values)
        {
            
            return $instance->push($key, ...$values);
        }

        public static function pop($key)
        {
            
            return $instance->pop($key);
        }

        public static function pushHidden($key, ...$values)
        {
            
            return $instance->pushHidden($key, ...$values);
        }

        public static function popHidden($key)
        {
            
            return $instance->popHidden($key);
        }

        public static function increment($key, $amount = 1)
        {
            
            return $instance->increment($key, $amount);
        }

        public static function decrement($key, $amount = 1)
        {
            
            return $instance->decrement($key, $amount);
        }

        public static function stackContains($key, $value, $strict = false)
        {
            
            return $instance->stackContains($key, $value, $strict);
        }

        public static function hiddenStackContains($key, $value, $strict = false)
        {
            
            return $instance->hiddenStackContains($key, $value, $strict);
        }

        public static function scope($callback, $data = [], $hidden = [])
        {
            
            return $instance->scope($callback, $data, $hidden);
        }

        public static function isEmpty()
        {
            
            return $instance->isEmpty();
        }

        public static function dehydrating($callback)
        {
            
            return $instance->dehydrating($callback);
        }

        public static function hydrated($callback)
        {
            
            return $instance->hydrated($callback);
        }

        public static function handleUnserializeExceptionsUsing($callback)
        {
            
            return $instance->handleUnserializeExceptionsUsing($callback);
        }

        public static function flush()
        {
            
            return $instance->flush();
        }

        public static function dehydrate()
        {
            
            return $instance->dehydrate();
        }

        public static function hydrate($context)
        {
            
            return $instance->hydrate($context);
        }

        public static function when($value = null, $callback = null, $default = null)
        {
            
            return $instance->when($value, $callback, $default);
        }

        public static function unless($value = null, $callback = null, $default = null)
        {
            
            return $instance->unless($value, $callback, $default);
        }

        public static function macro($name, $macro)
        {
            \Illuminate\Log\Context\Repository::macro($name, $macro);
        }

        public static function mixin($mixin, $replace = true)
        {
            \Illuminate\Log\Context\Repository::mixin($mixin, $replace);
        }

        public static function hasMacro($name)
        {
            return \Illuminate\Log\Context\Repository::hasMacro($name);
        }

        public static function flushMacros()
        {
            \Illuminate\Log\Context\Repository::flushMacros();
        }

        public static function restoreModel($value)
        {
            
            return $instance->restoreModel($value);
        }

            }
    
    class Cookie {
        
        public static function make($name, $value, $minutes = 0, $path = null, $domain = null, $secure = null, $httpOnly = true, $raw = false, $sameSite = null)
        {
            
            return $instance->make($name, $value, $minutes, $path, $domain, $secure, $httpOnly, $raw, $sameSite);
        }

        public static function forever($name, $value, $path = null, $domain = null, $secure = null, $httpOnly = true, $raw = false, $sameSite = null)
        {
            
            return $instance->forever($name, $value, $path, $domain, $secure, $httpOnly, $raw, $sameSite);
        }

        public static function forget($name, $path = null, $domain = null)
        {
            
            return $instance->forget($name, $path, $domain);
        }

        public static function hasQueued($key, $path = null)
        {
            
            return $instance->hasQueued($key, $path);
        }

        public static function queued($key, $default = null, $path = null)
        {
            
            return $instance->queued($key, $default, $path);
        }

        public static function queue(...$parameters)
        {
            
            $instance->queue(...$parameters);
        }

        public static function expire($name, $path = null, $domain = null)
        {
            
            $instance->expire($name, $path, $domain);
        }

        public static function unqueue($name, $path = null)
        {
            
            $instance->unqueue($name, $path);
        }

        public static function setDefaultPathAndDomain($path, $domain, $secure = false, $sameSite = null)
        {
            
            return $instance->setDefaultPathAndDomain($path, $domain, $secure, $sameSite);
        }

        public static function getQueuedCookies()
        {
            
            return $instance->getQueuedCookies();
        }

        public static function flushQueuedCookies()
        {
            
            return $instance->flushQueuedCookies();
        }

        public static function macro($name, $macro)
        {
            \Illuminate\Cookie\CookieJar::macro($name, $macro);
        }

        public static function mixin($mixin, $replace = true)
        {
            \Illuminate\Cookie\CookieJar::mixin($mixin, $replace);
        }

        public static function hasMacro($name)
        {
            return \Illuminate\Cookie\CookieJar::hasMacro($name);
        }

        public static function flushMacros()
        {
            \Illuminate\Cookie\CookieJar::flushMacros();
        }

            }
    
    class Crypt {
        
        public static function supported($key, $cipher)
        {
            return \Illuminate\Encryption\Encrypter::supported($key, $cipher);
        }

        public static function generateKey($cipher)
        {
            return \Illuminate\Encryption\Encrypter::generateKey($cipher);
        }

        public static function encrypt($value, $serialize = true)
        {
            
            return $instance->encrypt($value, $serialize);
        }

        public static function encryptString($value)
        {
            
            return $instance->encryptString($value);
        }

        public static function decrypt($payload, $unserialize = true)
        {
            
            return $instance->decrypt($payload, $unserialize);
        }

        public static function decryptString($payload)
        {
            
            return $instance->decryptString($payload);
        }

        public static function appearsEncrypted($value)
        {
            return \Illuminate\Encryption\Encrypter::appearsEncrypted($value);
        }

        public static function getKey()
        {
            
            return $instance->getKey();
        }

        public static function getAllKeys()
        {
            
            return $instance->getAllKeys();
        }

        public static function getPreviousKeys()
        {
            
            return $instance->getPreviousKeys();
        }

        public static function previousKeys($keys)
        {
            
            return $instance->previousKeys($keys);
        }

            }
    
    class Date {
        
        public static function use($handler)
        {
            return \Illuminate\Support\DateFactory::use($handler);
        }

        public static function useDefault()
        {
            \Illuminate\Support\DateFactory::useDefault();
        }

        public static function useCallable($callable)
        {
            \Illuminate\Support\DateFactory::useCallable($callable);
        }

        public static function useClass($dateClass)
        {
            \Illuminate\Support\DateFactory::useClass($dateClass);
        }

        public static function useFactory($factory)
        {
            \Illuminate\Support\DateFactory::useFactory($factory);
        }

            }
    
    class DB {
        
        public static function connection($name = null)
        {
            
            return $instance->connection($name);
        }

        public static function build($config)
        {
            
            return $instance->build($config);
        }

        public static function calculateDynamicConnectionName($config)
        {
            return \Illuminate\Database\DatabaseManager::calculateDynamicConnectionName($config);
        }

        public static function connectUsing($name, $config, $force = false)
        {
            
            return $instance->connectUsing($name, $config, $force);
        }

        public static function purge($name = null)
        {
            
            $instance->purge($name);
        }

        public static function disconnect($name = null)
        {
            
            $instance->disconnect($name);
        }

        public static function reconnect($name = null)
        {
            
            return $instance->reconnect($name);
        }

        public static function usingConnection($name, $callback)
        {
            
            return $instance->usingConnection($name, $callback);
        }

        public static function getDefaultConnection()
        {
            
            return $instance->getDefaultConnection();
        }

        public static function setDefaultConnection($name)
        {
            
            $instance->setDefaultConnection($name);
        }

        public static function supportedDrivers()
        {
            
            return $instance->supportedDrivers();
        }

        public static function availableDrivers()
        {
            
            return $instance->availableDrivers();
        }

        public static function extend($name, $resolver)
        {
            
            $instance->extend($name, $resolver);
        }

        public static function forgetExtension($name)
        {
            
            $instance->forgetExtension($name);
        }

        public static function getConnections()
        {
            
            return $instance->getConnections();
        }

        public static function setReconnector($reconnector)
        {
            
            $instance->setReconnector($reconnector);
        }

        public static function setApplication($app)
        {
            
            return $instance->setApplication($app);
        }

        public static function macro($name, $macro)
        {
            \Illuminate\Database\DatabaseManager::macro($name, $macro);
        }

        public static function mixin($mixin, $replace = true)
        {
            \Illuminate\Database\DatabaseManager::mixin($mixin, $replace);
        }

        public static function hasMacro($name)
        {
            return \Illuminate\Database\DatabaseManager::hasMacro($name);
        }

        public static function flushMacros()
        {
            \Illuminate\Database\DatabaseManager::flushMacros();
        }

        public static function macroCall($method, $parameters)
        {
            
            return $instance->macroCall($method, $parameters);
        }

        public static function getDriverTitle()
        {
            
            return $instance->getDriverTitle();
        }

        public static function getSchemaBuilder()
        {
            
            return $instance->getSchemaBuilder();
        }

        public static function getSchemaState($files = null, $processFactory = null)
        {
            
            return $instance->getSchemaState($files, $processFactory);
        }

        public static function useDefaultQueryGrammar()
        {

            $instance->useDefaultQueryGrammar();
        }

        public static function useDefaultSchemaGrammar()
        {

            $instance->useDefaultSchemaGrammar();
        }

        public static function useDefaultPostProcessor()
        {

            $instance->useDefaultPostProcessor();
        }

        public static function table($table, $as = null)
        {

            return $instance->table($table, $as);
        }

        public static function query()
        {

            return $instance->query();
        }

        public static function selectOne($query, $bindings = [], $useReadPdo = true)
        {

            return $instance->selectOne($query, $bindings, $useReadPdo);
        }

        public static function scalar($query, $bindings = [], $useReadPdo = true)
        {

            return $instance->scalar($query, $bindings, $useReadPdo);
        }

        public static function selectFromWriteConnection($query, $bindings = [])
        {

            return $instance->selectFromWriteConnection($query, $bindings);
        }

        public static function select($query, $bindings = [], $useReadPdo = true)
        {

            return $instance->select($query, $bindings, $useReadPdo);
        }

        public static function selectResultSets($query, $bindings = [], $useReadPdo = true)
        {

            return $instance->selectResultSets($query, $bindings, $useReadPdo);
        }

        public static function cursor($query, $bindings = [], $useReadPdo = true)
        {

            return $instance->cursor($query, $bindings, $useReadPdo);
        }

        public static function insert($query, $bindings = [])
        {

            return $instance->insert($query, $bindings);
        }

        public static function update($query, $bindings = [])
        {

            return $instance->update($query, $bindings);
        }

        public static function delete($query, $bindings = [])
        {

            return $instance->delete($query, $bindings);
        }

        public static function statement($query, $bindings = [])
        {

            return $instance->statement($query, $bindings);
        }

        public static function affectingStatement($query, $bindings = [])
        {

            return $instance->affectingStatement($query, $bindings);
        }

        public static function unprepared($query)
        {

            return $instance->unprepared($query);
        }

        public static function threadCount()
        {

            return $instance->threadCount();
        }

        public static function pretend($callback)
        {

            return $instance->pretend($callback);
        }

        public static function withoutPretending($callback)
        {

            return $instance->withoutPretending($callback);
        }

        public static function bindValues($statement, $bindings)
        {

            $instance->bindValues($statement, $bindings);
        }

        public static function prepareBindings($bindings)
        {

            return $instance->prepareBindings($bindings);
        }

        public static function logQuery($query, $bindings, $time = null)
        {

            $instance->logQuery($query, $bindings, $time);
        }

        public static function whenQueryingForLongerThan($threshold, $handler)
        {

            $instance->whenQueryingForLongerThan($threshold, $handler);
        }

        public static function allowQueryDurationHandlersToRunAgain()
        {

            $instance->allowQueryDurationHandlersToRunAgain();
        }

        public static function totalQueryDuration()
        {

            return $instance->totalQueryDuration();
        }

        public static function resetTotalQueryDuration()
        {

            $instance->resetTotalQueryDuration();
        }

        public static function reconnectIfMissingConnection()
        {

            $instance->reconnectIfMissingConnection();
        }

        public static function beforeStartingTransaction($callback)
        {

            return $instance->beforeStartingTransaction($callback);
        }

        public static function beforeExecuting($callback)
        {

            return $instance->beforeExecuting($callback);
        }

        public static function listen($callback)
        {

            $instance->listen($callback);
        }

        public static function raw($value)
        {

            return $instance->raw($value);
        }

        public static function escape($value, $binary = false)
        {

            return $instance->escape($value, $binary);
        }

        public static function hasModifiedRecords()
        {

            return $instance->hasModifiedRecords();
        }

        public static function recordsHaveBeenModified($value = true)
        {

            $instance->recordsHaveBeenModified($value);
        }

        public static function setRecordModificationState($value)
        {

            return $instance->setRecordModificationState($value);
        }

        public static function forgetRecordModificationState()
        {

            $instance->forgetRecordModificationState();
        }

        public static function useWriteConnectionWhenReading($value = true)
        {

            return $instance->useWriteConnectionWhenReading($value);
        }

        public static function getPdo()
        {

            return $instance->getPdo();
        }

        public static function getRawPdo()
        {

            return $instance->getRawPdo();
        }

        public static function getReadPdo()
        {

            return $instance->getReadPdo();
        }

        public static function getRawReadPdo()
        {

            return $instance->getRawReadPdo();
        }

        public static function setPdo($pdo)
        {

            return $instance->setPdo($pdo);
        }

        public static function setReadPdo($pdo)
        {

            return $instance->setReadPdo($pdo);
        }

        public static function setReadPdoConfig($config)
        {

            return $instance->setReadPdoConfig($config);
        }

        public static function getName()
        {

            return $instance->getName();
        }

        public static function getNameWithReadWriteType()
        {

            return $instance->getNameWithReadWriteType();
        }

        public static function getConfig($option = null)
        {

            return $instance->getConfig($option);
        }

        public static function getDriverName()
        {

            return $instance->getDriverName();
        }

        public static function getQueryGrammar()
        {

            return $instance->getQueryGrammar();
        }

        public static function setQueryGrammar($grammar)
        {

            return $instance->setQueryGrammar($grammar);
        }

        public static function getSchemaGrammar()
        {

            return $instance->getSchemaGrammar();
        }

        public static function setSchemaGrammar($grammar)
        {

            return $instance->setSchemaGrammar($grammar);
        }

        public static function getPostProcessor()
        {

            return $instance->getPostProcessor();
        }

        public static function setPostProcessor($processor)
        {

            return $instance->setPostProcessor($processor);
        }

        public static function getEventDispatcher()
        {

            return $instance->getEventDispatcher();
        }

        public static function setEventDispatcher($events)
        {

            return $instance->setEventDispatcher($events);
        }

        public static function unsetEventDispatcher()
        {

            $instance->unsetEventDispatcher();
        }

        public static function setTransactionManager($manager)
        {

            return $instance->setTransactionManager($manager);
        }

        public static function unsetTransactionManager()
        {

            $instance->unsetTransactionManager();
        }

        public static function pretending()
        {

            return $instance->pretending();
        }

        public static function getQueryLog()
        {

            return $instance->getQueryLog();
        }

        public static function getRawQueryLog()
        {

            return $instance->getRawQueryLog();
        }

        public static function flushQueryLog()
        {

            $instance->flushQueryLog();
        }

        public static function enableQueryLog()
        {

            $instance->enableQueryLog();
        }

        public static function disableQueryLog()
        {

            $instance->disableQueryLog();
        }

        public static function logging()
        {

            return $instance->logging();
        }

        public static function getDatabaseName()
        {

            return $instance->getDatabaseName();
        }

        public static function setDatabaseName($database)
        {

            return $instance->setDatabaseName($database);
        }

        public static function setReadWriteType($readWriteType)
        {

            return $instance->setReadWriteType($readWriteType);
        }

        public static function getTablePrefix()
        {

            return $instance->getTablePrefix();
        }

        public static function setTablePrefix($prefix)
        {

            return $instance->setTablePrefix($prefix);
        }

        public static function withoutTablePrefix($callback)
        {

            return $instance->withoutTablePrefix($callback);
        }

        public static function getServerVersion()
        {

            return $instance->getServerVersion();
        }

        public static function resolverFor($driver, $callback)
        {
            
            \Illuminate\Database\PostgresConnection::resolverFor($driver, $callback);
        }

        public static function getResolver($driver)
        {
            
            return \Illuminate\Database\PostgresConnection::getResolver($driver);
        }

        public static function transaction($callback, $attempts = 1)
        {

            return $instance->transaction($callback, $attempts);
        }

        public static function beginTransaction()
        {

            $instance->beginTransaction();
        }

        public static function commit()
        {

            $instance->commit();
        }

        public static function rollBack($toLevel = null)
        {

            $instance->rollBack($toLevel);
        }

        public static function transactionLevel()
        {

            return $instance->transactionLevel();
        }

        public static function afterCommit($callback)
        {

            $instance->afterCommit($callback);
        }

        public static function afterRollBack($callback)
        {

            $instance->afterRollBack($callback);
        }

            }
    
    class Event {
        
        public static function listen($events, $listener = null)
        {
            
            $instance->listen($events, $listener);
        }

        public static function hasListeners($eventName)
        {
            
            return $instance->hasListeners($eventName);
        }

        public static function hasWildcardListeners($eventName)
        {
            
            return $instance->hasWildcardListeners($eventName);
        }

        public static function push($event, $payload = [])
        {
            
            $instance->push($event, $payload);
        }

        public static function flush($event)
        {
            
            $instance->flush($event);
        }

        public static function subscribe($subscriber)
        {
            
            $instance->subscribe($subscriber);
        }

        public static function until($event, $payload = [])
        {
            
            return $instance->until($event, $payload);
        }

        public static function dispatch($event, $payload = [], $halt = false)
        {
            
            return $instance->dispatch($event, $payload, $halt);
        }

        public static function getListeners($eventName)
        {
            
            return $instance->getListeners($eventName);
        }

        public static function makeListener($listener, $wildcard = false)
        {
            
            return $instance->makeListener($listener, $wildcard);
        }

        public static function createClassListener($listener, $wildcard = false)
        {
            
            return $instance->createClassListener($listener, $wildcard);
        }

        public static function forget($event)
        {
            
            $instance->forget($event);
        }

        public static function forgetPushed()
        {
            
            $instance->forgetPushed();
        }

        public static function setQueueResolver($resolver)
        {
            
            return $instance->setQueueResolver($resolver);
        }

        public static function setTransactionManagerResolver($resolver)
        {
            
            return $instance->setTransactionManagerResolver($resolver);
        }

        public static function defer($callback, $events = null)
        {
            
            return $instance->defer($callback, $events);
        }

        public static function getRawListeners()
        {
            
            return $instance->getRawListeners();
        }

        public static function macro($name, $macro)
        {
            \Illuminate\Events\Dispatcher::macro($name, $macro);
        }

        public static function mixin($mixin, $replace = true)
        {
            \Illuminate\Events\Dispatcher::mixin($mixin, $replace);
        }

        public static function hasMacro($name)
        {
            return \Illuminate\Events\Dispatcher::hasMacro($name);
        }

        public static function flushMacros()
        {
            \Illuminate\Events\Dispatcher::flushMacros();
        }

        public static function except($eventsToDispatch)
        {
            
            return $instance->except($eventsToDispatch);
        }

        public static function assertListening($expectedEvent, $expectedListener)
        {
            
            $instance->assertListening($expectedEvent, $expectedListener);
        }

        public static function assertDispatched($event, $callback = null)
        {
            
            $instance->assertDispatched($event, $callback);
        }

        public static function assertDispatchedOnce($event)
        {
            
            $instance->assertDispatchedOnce($event);
        }

        public static function assertDispatchedTimes($event, $times = 1)
        {
            
            $instance->assertDispatchedTimes($event, $times);
        }

        public static function assertNotDispatched($event, $callback = null)
        {
            
            $instance->assertNotDispatched($event, $callback);
        }

        public static function assertNothingDispatched()
        {
            
            $instance->assertNothingDispatched();
        }

        public static function dispatched($event, $callback = null)
        {
            
            return $instance->dispatched($event, $callback);
        }

        public static function hasDispatched($event)
        {
            
            return $instance->hasDispatched($event);
        }

        public static function dispatchedEvents()
        {
            
            return $instance->dispatchedEvents();
        }

            }
    
    class File {
        
        public static function exists($path)
        {
            
            return $instance->exists($path);
        }

        public static function missing($path)
        {
            
            return $instance->missing($path);
        }

        public static function get($path, $lock = false)
        {
            
            return $instance->get($path, $lock);
        }

        public static function json($path, $flags = 0, $lock = false)
        {
            
            return $instance->json($path, $flags, $lock);
        }

        public static function sharedGet($path)
        {
            
            return $instance->sharedGet($path);
        }

        public static function getRequire($path, $data = [])
        {
            
            return $instance->getRequire($path, $data);
        }

        public static function requireOnce($path, $data = [])
        {
            
            return $instance->requireOnce($path, $data);
        }

        public static function lines($path)
        {
            
            return $instance->lines($path);
        }

        public static function hash($path, $algorithm = 'md5')
        {
            
            return $instance->hash($path, $algorithm);
        }

        public static function put($path, $contents, $lock = false)
        {
            
            return $instance->put($path, $contents, $lock);
        }

        public static function replace($path, $content, $mode = null)
        {
            
            $instance->replace($path, $content, $mode);
        }

        public static function replaceInFile($search, $replace, $path)
        {
            
            $instance->replaceInFile($search, $replace, $path);
        }

        public static function prepend($path, $data)
        {
            
            return $instance->prepend($path, $data);
        }

        public static function append($path, $data, $lock = false)
        {
            
            return $instance->append($path, $data, $lock);
        }

        public static function chmod($path, $mode = null)
        {
            
            return $instance->chmod($path, $mode);
        }

        public static function delete($paths)
        {
            
            return $instance->delete($paths);
        }

        public static function move($path, $target)
        {
            
            return $instance->move($path, $target);
        }

        public static function copy($path, $target)
        {
            
            return $instance->copy($path, $target);
        }

        public static function link($target, $link)
        {
            
            return $instance->link($target, $link);
        }

        public static function relativeLink($target, $link)
        {
            
            $instance->relativeLink($target, $link);
        }

        public static function name($path)
        {
            
            return $instance->name($path);
        }

        public static function basename($path)
        {
            
            return $instance->basename($path);
        }

        public static function dirname($path)
        {
            
            return $instance->dirname($path);
        }

        public static function extension($path)
        {
            
            return $instance->extension($path);
        }

        public static function guessExtension($path)
        {
            
            return $instance->guessExtension($path);
        }

        public static function type($path)
        {
            
            return $instance->type($path);
        }

        public static function mimeType($path)
        {
            
            return $instance->mimeType($path);
        }

        public static function size($path)
        {
            
            return $instance->size($path);
        }

        public static function lastModified($path)
        {
            
            return $instance->lastModified($path);
        }

        public static function isDirectory($directory)
        {
            
            return $instance->isDirectory($directory);
        }

        public static function isEmptyDirectory($directory, $ignoreDotFiles = false)
        {
            
            return $instance->isEmptyDirectory($directory, $ignoreDotFiles);
        }

        public static function isReadable($path)
        {
            
            return $instance->isReadable($path);
        }

        public static function isWritable($path)
        {
            
            return $instance->isWritable($path);
        }

        public static function hasSameHash($firstFile, $secondFile)
        {
            
            return $instance->hasSameHash($firstFile, $secondFile);
        }

        public static function isFile($file)
        {
            
            return $instance->isFile($file);
        }

        public static function glob($pattern, $flags = 0)
        {
            
            return $instance->glob($pattern, $flags);
        }

        public static function files($directory, $hidden = false, $depth = 0)
        {
            
            return $instance->files($directory, $hidden, $depth);
        }

        public static function allFiles($directory, $hidden = false)
        {
            
            return $instance->allFiles($directory, $hidden);
        }

        public static function directories($directory, $depth = 0)
        {
            
            return $instance->directories($directory, $depth);
        }

        public static function allDirectories($directory)
        {
            
            return $instance->allDirectories($directory);
        }

        public static function ensureDirectoryExists($path, $mode = 493, $recursive = true)
        {
            
            $instance->ensureDirectoryExists($path, $mode, $recursive);
        }

        public static function makeDirectory($path, $mode = 493, $recursive = false, $force = false)
        {
            
            return $instance->makeDirectory($path, $mode, $recursive, $force);
        }

        public static function moveDirectory($from, $to, $overwrite = false)
        {
            
            return $instance->moveDirectory($from, $to, $overwrite);
        }

        public static function copyDirectory($directory, $destination, $options = null)
        {
            
            return $instance->copyDirectory($directory, $destination, $options);
        }

        public static function deleteDirectory($directory, $preserve = false)
        {
            
            return $instance->deleteDirectory($directory, $preserve);
        }

        public static function deleteDirectories($directory)
        {
            
            return $instance->deleteDirectories($directory);
        }

        public static function cleanDirectory($directory)
        {
            
            return $instance->cleanDirectory($directory);
        }

        public static function when($value = null, $callback = null, $default = null)
        {
            
            return $instance->when($value, $callback, $default);
        }

        public static function unless($value = null, $callback = null, $default = null)
        {
            
            return $instance->unless($value, $callback, $default);
        }

        public static function macro($name, $macro)
        {
            \Illuminate\Filesystem\Filesystem::macro($name, $macro);
        }

        public static function mixin($mixin, $replace = true)
        {
            \Illuminate\Filesystem\Filesystem::mixin($mixin, $replace);
        }

        public static function hasMacro($name)
        {
            return \Illuminate\Filesystem\Filesystem::hasMacro($name);
        }

        public static function flushMacros()
        {
            \Illuminate\Filesystem\Filesystem::flushMacros();
        }

            }
    
    class Gate {
        
        public static function has($ability)
        {
            
            return $instance->has($ability);
        }

        public static function allowIf($condition, $message = null, $code = null)
        {
            
            return $instance->allowIf($condition, $message, $code);
        }

        public static function denyIf($condition, $message = null, $code = null)
        {
            
            return $instance->denyIf($condition, $message, $code);
        }

        public static function define($ability, $callback)
        {
            
            return $instance->define($ability, $callback);
        }

        public static function resource($name, $class, $abilities = null)
        {
            
            return $instance->resource($name, $class, $abilities);
        }

        public static function policy($class, $policy)
        {
            
            return $instance->policy($class, $policy);
        }

        public static function before($callback)
        {
            
            return $instance->before($callback);
        }

        public static function after($callback)
        {
            
            return $instance->after($callback);
        }

        public static function allows($ability, $arguments = [])
        {
            
            return $instance->allows($ability, $arguments);
        }

        public static function denies($ability, $arguments = [])
        {
            
            return $instance->denies($ability, $arguments);
        }

        public static function check($abilities, $arguments = [])
        {
            
            return $instance->check($abilities, $arguments);
        }

        public static function any($abilities, $arguments = [])
        {
            
            return $instance->any($abilities, $arguments);
        }

        public static function none($abilities, $arguments = [])
        {
            
            return $instance->none($abilities, $arguments);
        }

        public static function authorize($ability, $arguments = [])
        {
            
            return $instance->authorize($ability, $arguments);
        }

        public static function inspect($ability, $arguments = [])
        {
            
            return $instance->inspect($ability, $arguments);
        }

        public static function raw($ability, $arguments = [])
        {
            
            return $instance->raw($ability, $arguments);
        }

        public static function getPolicyFor($class)
        {
            
            return $instance->getPolicyFor($class);
        }

        public static function guessPolicyNamesUsing($callback)
        {
            
            return $instance->guessPolicyNamesUsing($callback);
        }

        public static function resolvePolicy($class)
        {
            
            return $instance->resolvePolicy($class);
        }

        public static function forUser($user)
        {
            
            return $instance->forUser($user);
        }

        public static function abilities()
        {
            
            return $instance->abilities();
        }

        public static function policies()
        {
            
            return $instance->policies();
        }

        public static function defaultDenialResponse($response)
        {
            
            return $instance->defaultDenialResponse($response);
        }

        public static function setContainer($container)
        {
            
            return $instance->setContainer($container);
        }

        public static function denyWithStatus($status, $message = null, $code = null)
        {
            
            return $instance->denyWithStatus($status, $message, $code);
        }

        public static function denyAsNotFound($message = null, $code = null)
        {
            
            return $instance->denyAsNotFound($message, $code);
        }

            }
    
    class Hash {
        
        public static function createBcryptDriver()
        {
            
            return $instance->createBcryptDriver();
        }

        public static function createArgonDriver()
        {
            
            return $instance->createArgonDriver();
        }

        public static function createArgon2idDriver()
        {
            
            return $instance->createArgon2idDriver();
        }

        public static function info($hashedValue)
        {
            
            return $instance->info($hashedValue);
        }

        public static function make($value, $options = [])
        {
            
            return $instance->make($value, $options);
        }

        public static function check($value, $hashedValue, $options = [])
        {
            
            return $instance->check($value, $hashedValue, $options);
        }

        public static function needsRehash($hashedValue, $options = [])
        {
            
            return $instance->needsRehash($hashedValue, $options);
        }

        public static function isHashed($value)
        {
            
            return $instance->isHashed($value);
        }

        public static function getDefaultDriver()
        {
            
            return $instance->getDefaultDriver();
        }

        public static function verifyConfiguration($value)
        {
            
            return $instance->verifyConfiguration($value);
        }

        public static function driver($driver = null)
        {

            return $instance->driver($driver);
        }

        public static function extend($driver, $callback)
        {

            return $instance->extend($driver, $callback);
        }

        public static function getDrivers()
        {

            return $instance->getDrivers();
        }

        public static function getContainer()
        {

            return $instance->getContainer();
        }

        public static function setContainer($container)
        {

            return $instance->setContainer($container);
        }

        public static function forgetDrivers()
        {

            return $instance->forgetDrivers();
        }

            }
    
    class Http {
        
        public static function globalMiddleware($middleware)
        {
            
            return $instance->globalMiddleware($middleware);
        }

        public static function globalRequestMiddleware($middleware)
        {
            
            return $instance->globalRequestMiddleware($middleware);
        }

        public static function globalResponseMiddleware($middleware)
        {
            
            return $instance->globalResponseMiddleware($middleware);
        }

        public static function globalOptions($options)
        {
            
            return $instance->globalOptions($options);
        }

        public static function response($body = null, $status = 200, $headers = [])
        {
            return \Illuminate\Http\Client\Factory::response($body, $status, $headers);
        }

        public static function psr7Response($body = null, $status = 200, $headers = [])
        {
            return \Illuminate\Http\Client\Factory::psr7Response($body, $status, $headers);
        }

        public static function failedRequest($body = null, $status = 200, $headers = [])
        {
            return \Illuminate\Http\Client\Factory::failedRequest($body, $status, $headers);
        }

        public static function failedConnection($message = null)
        {
            return \Illuminate\Http\Client\Factory::failedConnection($message);
        }

        public static function sequence($responses = [])
        {
            
            return $instance->sequence($responses);
        }

        public static function fake($callback = null)
        {
            
            return $instance->fake($callback);
        }

        public static function fakeSequence($url = '*')
        {
            
            return $instance->fakeSequence($url);
        }

        public static function stubUrl($url, $callback)
        {
            
            return $instance->stubUrl($url, $callback);
        }

        public static function preventStrayRequests($prevent = true)
        {
            
            return $instance->preventStrayRequests($prevent);
        }

        public static function preventingStrayRequests()
        {
            
            return $instance->preventingStrayRequests();
        }

        public static function allowStrayRequests($only = null)
        {
            
            return $instance->allowStrayRequests($only);
        }

        public static function record()
        {
            
            return $instance->record();
        }

        public static function recordRequestResponsePair($request, $response)
        {
            
            $instance->recordRequestResponsePair($request, $response);
        }

        public static function assertSent($callback)
        {
            
            $instance->assertSent($callback);
        }

        public static function assertSentInOrder($callbacks)
        {
            
            $instance->assertSentInOrder($callbacks);
        }

        public static function assertNotSent($callback)
        {
            
            $instance->assertNotSent($callback);
        }

        public static function assertNothingSent()
        {
            
            $instance->assertNothingSent();
        }

        public static function assertSentCount($count)
        {
            
            $instance->assertSentCount($count);
        }

        public static function assertSequencesAreEmpty()
        {
            
            $instance->assertSequencesAreEmpty();
        }

        public static function recorded($callback = null)
        {
            
            return $instance->recorded($callback);
        }

        public static function createPendingRequest()
        {
            
            return $instance->createPendingRequest();
        }

        public static function getDispatcher()
        {
            
            return $instance->getDispatcher();
        }

        public static function getGlobalMiddleware()
        {
            
            return $instance->getGlobalMiddleware();
        }

        public static function macro($name, $macro)
        {
            \Illuminate\Http\Client\Factory::macro($name, $macro);
        }

        public static function mixin($mixin, $replace = true)
        {
            \Illuminate\Http\Client\Factory::mixin($mixin, $replace);
        }

        public static function hasMacro($name)
        {
            return \Illuminate\Http\Client\Factory::hasMacro($name);
        }

        public static function flushMacros()
        {
            \Illuminate\Http\Client\Factory::flushMacros();
        }

        public static function macroCall($method, $parameters)
        {
            
            return $instance->macroCall($method, $parameters);
        }

            }
    
    class Lang {
        
        public static function hasForLocale($key, $locale = null)
        {
            
            return $instance->hasForLocale($key, $locale);
        }

        public static function has($key, $locale = null, $fallback = true)
        {
            
            return $instance->has($key, $locale, $fallback);
        }

        public static function get($key, $replace = [], $locale = null, $fallback = true)
        {
            
            return $instance->get($key, $replace, $locale, $fallback);
        }

        public static function choice($key, $number, $replace = [], $locale = null)
        {
            
            return $instance->choice($key, $number, $replace, $locale);
        }

        public static function addLines($lines, $locale, $namespace = '*')
        {
            
            $instance->addLines($lines, $locale, $namespace);
        }

        public static function load($namespace, $group, $locale)
        {
            
            $instance->load($namespace, $group, $locale);
        }

        public static function handleMissingKeysUsing($callback)
        {
            
            return $instance->handleMissingKeysUsing($callback);
        }

        public static function addNamespace($namespace, $hint)
        {
            
            $instance->addNamespace($namespace, $hint);
        }

        public static function addPath($path)
        {
            
            $instance->addPath($path);
        }

        public static function addJsonPath($path)
        {
            
            $instance->addJsonPath($path);
        }

        public static function parseKey($key)
        {
            
            return $instance->parseKey($key);
        }

        public static function determineLocalesUsing($callback)
        {
            
            $instance->determineLocalesUsing($callback);
        }

        public static function getSelector()
        {
            
            return $instance->getSelector();
        }

        public static function setSelector($selector)
        {
            
            $instance->setSelector($selector);
        }

        public static function getLoader()
        {
            
            return $instance->getLoader();
        }

        public static function locale()
        {
            
            return $instance->locale();
        }

        public static function getLocale()
        {
            
            return $instance->getLocale();
        }

        public static function setLocale($locale)
        {
            
            $instance->setLocale($locale);
        }

        public static function getFallback()
        {
            
            return $instance->getFallback();
        }

        public static function setFallback($fallback)
        {
            
            $instance->setFallback($fallback);
        }

        public static function setLoaded($loaded)
        {
            
            $instance->setLoaded($loaded);
        }

        public static function stringable($class, $handler = null)
        {
            
            $instance->stringable($class, $handler);
        }

        public static function setParsedKey($key, $parsed)
        {

            $instance->setParsedKey($key, $parsed);
        }

        public static function flushParsedKeys()
        {

            $instance->flushParsedKeys();
        }

        public static function macro($name, $macro)
        {
            \Illuminate\Translation\Translator::macro($name, $macro);
        }

        public static function mixin($mixin, $replace = true)
        {
            \Illuminate\Translation\Translator::mixin($mixin, $replace);
        }

        public static function hasMacro($name)
        {
            return \Illuminate\Translation\Translator::hasMacro($name);
        }

        public static function flushMacros()
        {
            \Illuminate\Translation\Translator::flushMacros();
        }

            }
    
    class Log {
        
        public static function build($config)
        {
            
            return $instance->build($config);
        }

        public static function stack($channels, $channel = null)
        {
            
            return $instance->stack($channels, $channel);
        }

        public static function channel($channel = null)
        {
            
            return $instance->channel($channel);
        }

        public static function driver($driver = null)
        {
            
            return $instance->driver($driver);
        }

        public static function shareContext($context)
        {
            
            return $instance->shareContext($context);
        }

        public static function sharedContext()
        {
            
            return $instance->sharedContext();
        }

        public static function withoutContext($keys = null)
        {
            
            return $instance->withoutContext($keys);
        }

        public static function flushSharedContext()
        {
            
            return $instance->flushSharedContext();
        }

        public static function getDefaultDriver()
        {
            
            return $instance->getDefaultDriver();
        }

        public static function setDefaultDriver($name)
        {
            
            $instance->setDefaultDriver($name);
        }

        public static function extend($driver, $callback)
        {
            
            return $instance->extend($driver, $callback);
        }

        public static function forgetChannel($driver = null)
        {
            
            $instance->forgetChannel($driver);
        }

        public static function getChannels()
        {
            
            return $instance->getChannels();
        }

        public static function emergency($message, $context = [])
        {
            
            $instance->emergency($message, $context);
        }

        public static function alert($message, $context = [])
        {
            
            $instance->alert($message, $context);
        }

        public static function critical($message, $context = [])
        {
            
            $instance->critical($message, $context);
        }

        public static function error($message, $context = [])
        {
            
            $instance->error($message, $context);
        }

        public static function warning($message, $context = [])
        {
            
            $instance->warning($message, $context);
        }

        public static function notice($message, $context = [])
        {
            
            $instance->notice($message, $context);
        }

        public static function info($message, $context = [])
        {
            
            $instance->info($message, $context);
        }

        public static function debug($message, $context = [])
        {
            
            $instance->debug($message, $context);
        }

        public static function log($level, $message, $context = [])
        {
            
            $instance->log($level, $message, $context);
        }

        public static function setApplication($app)
        {
            
            return $instance->setApplication($app);
        }

            }
    
    class Mail {
        
        public static function mailer($name = null)
        {
            
            return $instance->mailer($name);
        }

        public static function driver($driver = null)
        {
            
            return $instance->driver($driver);
        }

        public static function build($config)
        {
            
            return $instance->build($config);
        }

        public static function createSymfonyTransport($config)
        {
            
            return $instance->createSymfonyTransport($config);
        }

        public static function getDefaultDriver()
        {
            
            return $instance->getDefaultDriver();
        }

        public static function setDefaultDriver($name)
        {
            
            $instance->setDefaultDriver($name);
        }

        public static function purge($name = null)
        {
            
            $instance->purge($name);
        }

        public static function extend($driver, $callback)
        {
            
            return $instance->extend($driver, $callback);
        }

        public static function getApplication()
        {
            
            return $instance->getApplication();
        }

        public static function setApplication($app)
        {
            
            return $instance->setApplication($app);
        }

        public static function forgetMailers()
        {
            
            return $instance->forgetMailers();
        }

        public static function assertSent($mailable, $callback = null)
        {
            
            $instance->assertSent($mailable, $callback);
        }

        public static function assertSentTimes($mailable, $times = 1)
        {
            
            $instance->assertSentTimes($mailable, $times);
        }

        public static function assertNotOutgoing($mailable, $callback = null)
        {
            
            $instance->assertNotOutgoing($mailable, $callback);
        }

        public static function assertNotSent($mailable, $callback = null)
        {
            
            $instance->assertNotSent($mailable, $callback);
        }

        public static function assertNothingOutgoing()
        {
            
            $instance->assertNothingOutgoing();
        }

        public static function assertNothingSent()
        {
            
            $instance->assertNothingSent();
        }

        public static function assertQueued($mailable, $callback = null)
        {
            
            $instance->assertQueued($mailable, $callback);
        }

        public static function assertNotQueued($mailable, $callback = null)
        {
            
            $instance->assertNotQueued($mailable, $callback);
        }

        public static function assertNothingQueued()
        {
            
            $instance->assertNothingQueued();
        }

        public static function assertSentCount($count)
        {
            
            $instance->assertSentCount($count);
        }

        public static function assertQueuedCount($count)
        {
            
            $instance->assertQueuedCount($count);
        }

        public static function assertOutgoingCount($count)
        {
            
            $instance->assertOutgoingCount($count);
        }

        public static function sent($mailable, $callback = null)
        {
            
            return $instance->sent($mailable, $callback);
        }

        public static function hasSent($mailable)
        {
            
            return $instance->hasSent($mailable);
        }

        public static function queued($mailable, $callback = null)
        {
            
            return $instance->queued($mailable, $callback);
        }

        public static function hasQueued($mailable)
        {
            
            return $instance->hasQueued($mailable);
        }

        public static function to($users)
        {
            
            return $instance->to($users);
        }

        public static function cc($users)
        {
            
            return $instance->cc($users);
        }

        public static function bcc($users)
        {
            
            return $instance->bcc($users);
        }

        public static function raw($text, $callback)
        {
            
            $instance->raw($text, $callback);
        }

        public static function send($view, $data = [], $callback = null)
        {
            
            return $instance->send($view, $data, $callback);
        }

        public static function sendNow($mailable, $data = [], $callback = null)
        {
            
            $instance->sendNow($mailable, $data, $callback);
        }

        public static function queue($view, $queue = null)
        {
            
            return $instance->queue($view, $queue);
        }

        public static function later($delay, $view, $queue = null)
        {
            
            return $instance->later($delay, $view, $queue);
        }

            }
    
    class Notification {
        
        public static function send($notifiables, $notification)
        {
            
            $instance->send($notifiables, $notification);
        }

        public static function sendNow($notifiables, $notification, $channels = null)
        {
            
            $instance->sendNow($notifiables, $notification, $channels);
        }

        public static function channel($name = null)
        {
            
            return $instance->channel($name);
        }

        public static function getDefaultDriver()
        {
            
            return $instance->getDefaultDriver();
        }

        public static function deliversVia()
        {
            
            return $instance->deliversVia();
        }

        public static function deliverVia($channel)
        {
            
            $instance->deliverVia($channel);
        }

        public static function locale($locale)
        {
            
            return $instance->locale($locale);
        }

        public static function driver($driver = null)
        {

            return $instance->driver($driver);
        }

        public static function extend($driver, $callback)
        {

            return $instance->extend($driver, $callback);
        }

        public static function getDrivers()
        {

            return $instance->getDrivers();
        }

        public static function getContainer()
        {

            return $instance->getContainer();
        }

        public static function setContainer($container)
        {

            return $instance->setContainer($container);
        }

        public static function forgetDrivers()
        {

            return $instance->forgetDrivers();
        }

        public static function macro($name, $macro)
        {
            \Illuminate\Notifications\ChannelManager::macro($name, $macro);
        }

        public static function mixin($mixin, $replace = true)
        {
            \Illuminate\Notifications\ChannelManager::mixin($mixin, $replace);
        }

        public static function hasMacro($name)
        {
            return \Illuminate\Notifications\ChannelManager::hasMacro($name);
        }

        public static function flushMacros()
        {
            \Illuminate\Notifications\ChannelManager::flushMacros();
        }

        public static function assertSentOnDemand($notification, $callback = null)
        {
            
            $instance->assertSentOnDemand($notification, $callback);
        }

        public static function assertSentTo($notifiable, $notification, $callback = null)
        {
            
            $instance->assertSentTo($notifiable, $notification, $callback);
        }

        public static function assertSentOnDemandTimes($notification, $times = 1)
        {
            
            $instance->assertSentOnDemandTimes($notification, $times);
        }

        public static function assertSentToTimes($notifiable, $notification, $times = 1)
        {
            
            $instance->assertSentToTimes($notifiable, $notification, $times);
        }

        public static function assertNotSentTo($notifiable, $notification, $callback = null)
        {
            
            $instance->assertNotSentTo($notifiable, $notification, $callback);
        }

        public static function assertNothingSent()
        {
            
            $instance->assertNothingSent();
        }

        public static function assertNothingSentTo($notifiable)
        {
            
            $instance->assertNothingSentTo($notifiable);
        }

        public static function assertSentTimes($notification, $expectedCount)
        {
            
            $instance->assertSentTimes($notification, $expectedCount);
        }

        public static function assertCount($expectedCount)
        {
            
            $instance->assertCount($expectedCount);
        }

        public static function sent($notifiable, $notification, $callback = null)
        {
            
            return $instance->sent($notifiable, $notification, $callback);
        }

        public static function hasSent($notifiable, $notification)
        {
            
            return $instance->hasSent($notifiable, $notification);
        }

        public static function serializeAndRestore($serializeAndRestore = true)
        {
            
            return $instance->serializeAndRestore($serializeAndRestore);
        }

        public static function sentNotifications()
        {
            
            return $instance->sentNotifications();
        }

            }
    
    class Password {
        
        public static function broker($name = null)
        {
            
            return $instance->broker($name);
        }

        public static function getDefaultDriver()
        {
            
            return $instance->getDefaultDriver();
        }

        public static function setDefaultDriver($name)
        {
            
            $instance->setDefaultDriver($name);
        }

            }
    
    class Process {
        
        public static function result($output = '', $errorOutput = '', $exitCode = 0)
        {
            
            return $instance->result($output, $errorOutput, $exitCode);
        }

        public static function describe()
        {
            
            return $instance->describe();
        }

        public static function sequence($processes = [])
        {
            
            return $instance->sequence($processes);
        }

        public static function fake($callback = null)
        {
            
            return $instance->fake($callback);
        }

        public static function isRecording()
        {
            
            return $instance->isRecording();
        }

        public static function recordIfRecording($process, $result)
        {
            
            return $instance->recordIfRecording($process, $result);
        }

        public static function record($process, $result)
        {
            
            return $instance->record($process, $result);
        }

        public static function preventStrayProcesses($prevent = true)
        {
            
            return $instance->preventStrayProcesses($prevent);
        }

        public static function preventingStrayProcesses()
        {
            
            return $instance->preventingStrayProcesses();
        }

        public static function assertRan($callback)
        {
            
            return $instance->assertRan($callback);
        }

        public static function assertRanTimes($callback, $times = 1)
        {
            
            return $instance->assertRanTimes($callback, $times);
        }

        public static function assertNotRan($callback)
        {
            
            return $instance->assertNotRan($callback);
        }

        public static function assertDidntRun($callback)
        {
            
            return $instance->assertDidntRun($callback);
        }

        public static function assertNothingRan()
        {
            
            return $instance->assertNothingRan();
        }

        public static function pool($callback)
        {
            
            return $instance->pool($callback);
        }

        public static function pipe($callback, $output = null)
        {
            
            return $instance->pipe($callback, $output);
        }

        public static function concurrently($callback, $output = null)
        {
            
            return $instance->concurrently($callback, $output);
        }

        public static function newPendingProcess()
        {
            
            return $instance->newPendingProcess();
        }

        public static function macro($name, $macro)
        {
            \Illuminate\Process\Factory::macro($name, $macro);
        }

        public static function mixin($mixin, $replace = true)
        {
            \Illuminate\Process\Factory::mixin($mixin, $replace);
        }

        public static function hasMacro($name)
        {
            return \Illuminate\Process\Factory::hasMacro($name);
        }

        public static function flushMacros()
        {
            \Illuminate\Process\Factory::flushMacros();
        }

        public static function macroCall($method, $parameters)
        {
            
            return $instance->macroCall($method, $parameters);
        }

            }
    
    class Queue {
        
        public static function before($callback)
        {
            
            $instance->before($callback);
        }

        public static function after($callback)
        {
            
            $instance->after($callback);
        }

        public static function exceptionOccurred($callback)
        {
            
            $instance->exceptionOccurred($callback);
        }

        public static function looping($callback)
        {
            
            $instance->looping($callback);
        }

        public static function failing($callback)
        {
            
            $instance->failing($callback);
        }

        public static function starting($callback)
        {
            
            $instance->starting($callback);
        }

        public static function stopping($callback)
        {
            
            $instance->stopping($callback);
        }

        public static function connected($name = null)
        {
            
            return $instance->connected($name);
        }

        public static function connection($name = null)
        {
            
            return $instance->connection($name);
        }

        public static function pause($connection, $queue)
        {
            
            $instance->pause($connection, $queue);
        }

        public static function pauseFor($connection, $queue, $ttl)
        {
            
            $instance->pauseFor($connection, $queue, $ttl);
        }

        public static function resume($connection, $queue)
        {
            
            $instance->resume($connection, $queue);
        }

        public static function isPaused($connection, $queue)
        {
            
            return $instance->isPaused($connection, $queue);
        }

        public static function withoutInterruptionPolling()
        {
            
            $instance->withoutInterruptionPolling();
        }

        public static function extend($driver, $resolver)
        {
            
            $instance->extend($driver, $resolver);
        }

        public static function addConnector($driver, $resolver)
        {
            
            $instance->addConnector($driver, $resolver);
        }

        public static function getDefaultDriver()
        {
            
            return $instance->getDefaultDriver();
        }

        public static function setDefaultDriver($name)
        {
            
            $instance->setDefaultDriver($name);
        }

        public static function getName($connection = null)
        {
            
            return $instance->getName($connection);
        }

        public static function getApplication()
        {
            
            return $instance->getApplication();
        }

        public static function setApplication($app)
        {
            
            return $instance->setApplication($app);
        }

        public static function except($jobsToBeQueued)
        {
            
            return $instance->except($jobsToBeQueued);
        }

        public static function assertPushed($job, $callback = null)
        {
            
            $instance->assertPushed($job, $callback);
        }

        public static function assertPushedOn($queue, $job, $callback = null)
        {
            
            $instance->assertPushedOn($queue, $job, $callback);
        }

        public static function assertPushedWithChain($job, $expectedChain = [], $callback = null)
        {
            
            $instance->assertPushedWithChain($job, $expectedChain, $callback);
        }

        public static function assertPushedWithoutChain($job, $callback = null)
        {
            
            $instance->assertPushedWithoutChain($job, $callback);
        }

        public static function assertClosurePushed($callback = null)
        {
            
            $instance->assertClosurePushed($callback);
        }

        public static function assertClosureNotPushed($callback = null)
        {
            
            $instance->assertClosureNotPushed($callback);
        }

        public static function assertNotPushed($job, $callback = null)
        {
            
            $instance->assertNotPushed($job, $callback);
        }

        public static function assertCount($expectedCount)
        {
            
            $instance->assertCount($expectedCount);
        }

        public static function assertNothingPushed()
        {
            
            $instance->assertNothingPushed();
        }

        public static function pushed($job, $callback = null)
        {
            
            return $instance->pushed($job, $callback);
        }

        public static function pushedRaw($callback = null)
        {
            
            return $instance->pushedRaw($callback);
        }

        public static function listenersPushed($listenerClass, $callback = null)
        {
            
            return $instance->listenersPushed($listenerClass, $callback);
        }

        public static function hasPushed($job)
        {
            
            return $instance->hasPushed($job);
        }

        public static function size($queue = null)
        {
            
            return $instance->size($queue);
        }

        public static function pendingSize($queue = null)
        {
            
            return $instance->pendingSize($queue);
        }

        public static function delayedSize($queue = null)
        {
            
            return $instance->delayedSize($queue);
        }

        public static function reservedSize($queue = null)
        {
            
            return $instance->reservedSize($queue);
        }

        public static function creationTimeOfOldestPendingJob($queue = null)
        {
            
            return $instance->creationTimeOfOldestPendingJob($queue);
        }

        public static function push($job, $data = '', $queue = null)
        {
            
            return $instance->push($job, $data, $queue);
        }

        public static function shouldFakeJob($job)
        {
            
            return $instance->shouldFakeJob($job);
        }

        public static function pushRaw($payload, $queue = null, $options = [])
        {
            
            return $instance->pushRaw($payload, $queue, $options);
        }

        public static function later($delay, $job, $data = '', $queue = null)
        {
            
            return $instance->later($delay, $job, $data, $queue);
        }

        public static function pushOn($queue, $job, $data = '')
        {
            
            return $instance->pushOn($queue, $job, $data);
        }

        public static function laterOn($queue, $delay, $job, $data = '')
        {
            
            return $instance->laterOn($queue, $delay, $job, $data);
        }

        public static function pop($queue = null)
        {
            
            return $instance->pop($queue);
        }

        public static function bulk($jobs, $data = '', $queue = null)
        {
            
            return $instance->bulk($jobs, $data, $queue);
        }

        public static function pushedJobs()
        {
            
            return $instance->pushedJobs();
        }

        public static function rawPushes()
        {
            
            return $instance->rawPushes();
        }

        public static function serializeAndRestore($serializeAndRestore = true)
        {
            
            return $instance->serializeAndRestore($serializeAndRestore);
        }

        public static function getConnectionName()
        {
            
            return $instance->getConnectionName();
        }

        public static function setConnectionName($name)
        {
            
            return $instance->setConnectionName($name);
        }

        public static function release($queue, $job, $delay)
        {
            
            return $instance->release($queue, $job, $delay);
        }

        public static function deleteReserved($queue, $id)
        {
            
            $instance->deleteReserved($queue, $id);
        }

        public static function deleteAndRelease($queue, $job, $delay)
        {
            
            $instance->deleteAndRelease($queue, $job, $delay);
        }

        public static function clear($queue)
        {
            
            return $instance->clear($queue);
        }

        public static function getQueue($queue)
        {
            
            return $instance->getQueue($queue);
        }

        public static function getDatabase()
        {
            
            return $instance->getDatabase();
        }

        public static function getJobTries($job)
        {

            return $instance->getJobTries($job);
        }

        public static function getJobBackoff($job)
        {

            return $instance->getJobBackoff($job);
        }

        public static function getJobExpiration($job)
        {

            return $instance->getJobExpiration($job);
        }

        public static function createPayloadUsing($callback)
        {
            
            \Illuminate\Queue\DatabaseQueue::createPayloadUsing($callback);
        }

        public static function getConfig()
        {

            return $instance->getConfig();
        }

        public static function setConfig($config)
        {

            return $instance->setConfig($config);
        }

        public static function getContainer()
        {

            return $instance->getContainer();
        }

        public static function setContainer($container)
        {

            $instance->setContainer($container);
        }

            }
    
    class RateLimiter {
        
        public static function for($name, $callback)
        {
            
            return $instance->for($name, $callback);
        }

        public static function limiter($name)
        {
            
            return $instance->limiter($name);
        }

        public static function attempt($key, $maxAttempts, $callback, $decaySeconds = 60)
        {
            
            return $instance->attempt($key, $maxAttempts, $callback, $decaySeconds);
        }

        public static function tooManyAttempts($key, $maxAttempts)
        {
            
            return $instance->tooManyAttempts($key, $maxAttempts);
        }

        public static function hit($key, $decaySeconds = 60)
        {
            
            return $instance->hit($key, $decaySeconds);
        }

        public static function increment($key, $decaySeconds = 60, $amount = 1)
        {
            
            return $instance->increment($key, $decaySeconds, $amount);
        }

        public static function decrement($key, $decaySeconds = 60, $amount = 1)
        {
            
            return $instance->decrement($key, $decaySeconds, $amount);
        }

        public static function attempts($key)
        {
            
            return $instance->attempts($key);
        }

        public static function resetAttempts($key)
        {
            
            return $instance->resetAttempts($key);
        }

        public static function remaining($key, $maxAttempts)
        {
            
            return $instance->remaining($key, $maxAttempts);
        }

        public static function retriesLeft($key, $maxAttempts)
        {
            
            return $instance->retriesLeft($key, $maxAttempts);
        }

        public static function clear($key)
        {
            
            $instance->clear($key);
        }

        public static function availableIn($key)
        {
            
            return $instance->availableIn($key);
        }

        public static function cleanRateLimiterKey($key)
        {
            
            return $instance->cleanRateLimiterKey($key);
        }

            }
    
    class Redirect {
        
        public static function back($status = 302, $headers = [], $fallback = false)
        {
            
            return $instance->back($status, $headers, $fallback);
        }

        public static function refresh($status = 302, $headers = [])
        {
            
            return $instance->refresh($status, $headers);
        }

        public static function guest($path, $status = 302, $headers = [], $secure = null)
        {
            
            return $instance->guest($path, $status, $headers, $secure);
        }

        public static function intended($default = '/', $status = 302, $headers = [], $secure = null)
        {
            
            return $instance->intended($default, $status, $headers, $secure);
        }

        public static function to($path, $status = 302, $headers = [], $secure = null)
        {
            
            return $instance->to($path, $status, $headers, $secure);
        }

        public static function away($path, $status = 302, $headers = [])
        {
            
            return $instance->away($path, $status, $headers);
        }

        public static function secure($path, $status = 302, $headers = [])
        {
            
            return $instance->secure($path, $status, $headers);
        }

        public static function route($route, $parameters = [], $status = 302, $headers = [])
        {
            
            return $instance->route($route, $parameters, $status, $headers);
        }

        public static function signedRoute($route, $parameters = [], $expiration = null, $status = 302, $headers = [])
        {
            
            return $instance->signedRoute($route, $parameters, $expiration, $status, $headers);
        }

        public static function temporarySignedRoute($route, $expiration, $parameters = [], $status = 302, $headers = [])
        {
            
            return $instance->temporarySignedRoute($route, $expiration, $parameters, $status, $headers);
        }

        public static function action($action, $parameters = [], $status = 302, $headers = [])
        {
            
            return $instance->action($action, $parameters, $status, $headers);
        }

        public static function getUrlGenerator()
        {
            
            return $instance->getUrlGenerator();
        }

        public static function setSession($session)
        {
            
            $instance->setSession($session);
        }

        public static function getIntendedUrl()
        {
            
            return $instance->getIntendedUrl();
        }

        public static function setIntendedUrl($url)
        {
            
            return $instance->setIntendedUrl($url);
        }

        public static function macro($name, $macro)
        {
            \Illuminate\Routing\Redirector::macro($name, $macro);
        }

        public static function mixin($mixin, $replace = true)
        {
            \Illuminate\Routing\Redirector::mixin($mixin, $replace);
        }

        public static function hasMacro($name)
        {
            return \Illuminate\Routing\Redirector::hasMacro($name);
        }

        public static function flushMacros()
        {
            \Illuminate\Routing\Redirector::flushMacros();
        }

            }
    
    class Request {
        
        public static function capture()
        {
            return \Illuminate\Http\Request::capture();
        }

        public static function instance()
        {
            
            return $instance->instance();
        }

        public static function method()
        {
            
            return $instance->method();
        }

        public static function uri()
        {
            
            return $instance->uri();
        }

        public static function root()
        {
            
            return $instance->root();
        }

        public static function url()
        {
            
            return $instance->url();
        }

        public static function fullUrl()
        {
            
            return $instance->fullUrl();
        }

        public static function fullUrlWithQuery($query)
        {
            
            return $instance->fullUrlWithQuery($query);
        }

        public static function fullUrlWithoutQuery($keys)
        {
            
            return $instance->fullUrlWithoutQuery($keys);
        }

        public static function path()
        {
            
            return $instance->path();
        }

        public static function decodedPath()
        {
            
            return $instance->decodedPath();
        }

        public static function segment($index, $default = null)
        {
            
            return $instance->segment($index, $default);
        }

        public static function segments()
        {
            
            return $instance->segments();
        }

        public static function is(...$patterns)
        {
            
            return $instance->is(...$patterns);
        }

        public static function routeIs(...$patterns)
        {
            
            return $instance->routeIs(...$patterns);
        }

        public static function fullUrlIs(...$patterns)
        {
            
            return $instance->fullUrlIs(...$patterns);
        }

        public static function host()
        {
            
            return $instance->host();
        }

        public static function httpHost()
        {
            
            return $instance->httpHost();
        }

        public static function schemeAndHttpHost()
        {
            
            return $instance->schemeAndHttpHost();
        }

        public static function ajax()
        {
            
            return $instance->ajax();
        }

        public static function pjax()
        {
            
            return $instance->pjax();
        }

        public static function prefetch()
        {
            
            return $instance->prefetch();
        }

        public static function secure()
        {
            
            return $instance->secure();
        }

        public static function ip()
        {
            
            return $instance->ip();
        }

        public static function ips()
        {
            
            return $instance->ips();
        }

        public static function userAgent()
        {
            
            return $instance->userAgent();
        }

        public static function getAcceptableContentTypes()
        {
            
            return $instance->getAcceptableContentTypes();
        }

        public static function merge($input)
        {
            
            return $instance->merge($input);
        }

        public static function mergeIfMissing($input)
        {
            
            return $instance->mergeIfMissing($input);
        }

        public static function replace($input)
        {
            
            return $instance->replace($input);
        }

        public static function get($key, $default = null)
        {
            
            return $instance->get($key, $default);
        }

        public static function json($key = null, $default = null)
        {
            
            return $instance->json($key, $default);
        }

        public static function createFrom($from, $to = null)
        {
            return \Illuminate\Http\Request::createFrom($from, $to);
        }

        public static function createFromBase($request)
        {
            return \Illuminate\Http\Request::createFromBase($request);
        }

        public static function duplicate($query = null, $request = null, $attributes = null, $cookies = null, $files = null, $server = null)
        {
            
            return $instance->duplicate($query, $request, $attributes, $cookies, $files, $server);
        }

        public static function hasSession($skipIfUninitialized = false)
        {
            
            return $instance->hasSession($skipIfUninitialized);
        }

        public static function getSession()
        {
            
            return $instance->getSession();
        }

        public static function session()
        {
            
            return $instance->session();
        }

        public static function setLaravelSession($session)
        {
            
            $instance->setLaravelSession($session);
        }

        public static function setRequestLocale($locale)
        {
            
            $instance->setRequestLocale($locale);
        }

        public static function setDefaultRequestLocale($locale)
        {
            
            $instance->setDefaultRequestLocale($locale);
        }

        public static function user($guard = null)
        {
            
            return $instance->user($guard);
        }

        public static function route($param = null, $default = null)
        {
            
            return $instance->route($param, $default);
        }

        public static function fingerprint()
        {
            
            return $instance->fingerprint();
        }

        public static function setJson($json)
        {
            
            return $instance->setJson($json);
        }

        public static function getUserResolver()
        {
            
            return $instance->getUserResolver();
        }

        public static function setUserResolver($callback)
        {
            
            return $instance->setUserResolver($callback);
        }

        public static function getRouteResolver()
        {
            
            return $instance->getRouteResolver();
        }

        public static function setRouteResolver($callback)
        {
            
            return $instance->setRouteResolver($callback);
        }

        public static function toArray()
        {
            
            return $instance->toArray();
        }

        public static function offsetExists($offset)
        {
            
            return $instance->offsetExists($offset);
        }

        public static function offsetGet($offset)
        {
            
            return $instance->offsetGet($offset);
        }

        public static function offsetSet($offset, $value)
        {
            
            $instance->offsetSet($offset, $value);
        }

        public static function offsetUnset($offset)
        {
            
            $instance->offsetUnset($offset);
        }

        public static function initialize($query = [], $request = [], $attributes = [], $cookies = [], $files = [], $server = [], $content = null)
        {

            return $instance->initialize($query, $request, $attributes, $cookies, $files, $server, $content);
        }

        public static function createFromGlobals()
        {
            
            return \Illuminate\Http\Request::createFromGlobals();
        }

        public static function create($uri, $method = 'GET', $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
        {
            
            return \Illuminate\Http\Request::create($uri, $method, $parameters, $cookies, $files, $server, $content);
        }

        public static function setFactory($callable)
        {
            
            return \Illuminate\Http\Request::setFactory($callable);
        }

        public static function overrideGlobals()
        {

            return $instance->overrideGlobals();
        }

        public static function setTrustedProxies($proxies, $trustedHeaderSet)
        {
            
            return \Illuminate\Http\Request::setTrustedProxies($proxies, $trustedHeaderSet);
        }

        public static function getTrustedProxies()
        {
            
            return \Illuminate\Http\Request::getTrustedProxies();
        }

        public static function getTrustedHeaderSet()
        {
            
            return \Illuminate\Http\Request::getTrustedHeaderSet();
        }

        public static function setTrustedHosts($hostPatterns)
        {
            
            return \Illuminate\Http\Request::setTrustedHosts($hostPatterns);
        }

        public static function getTrustedHosts()
        {
            
            return \Illuminate\Http\Request::getTrustedHosts();
        }

        public static function normalizeQueryString($qs)
        {
            
            return \Illuminate\Http\Request::normalizeQueryString($qs);
        }

        public static function enableHttpMethodParameterOverride()
        {
            
            return \Illuminate\Http\Request::enableHttpMethodParameterOverride();
        }

        public static function getHttpMethodParameterOverride()
        {
            
            return \Illuminate\Http\Request::getHttpMethodParameterOverride();
        }

        public static function setAllowedHttpMethodOverride($methods)
        {
            
            return \Illuminate\Http\Request::setAllowedHttpMethodOverride($methods);
        }

        public static function getAllowedHttpMethodOverride()
        {
            
            return \Illuminate\Http\Request::getAllowedHttpMethodOverride();
        }

        public static function hasPreviousSession()
        {

            return $instance->hasPreviousSession();
        }

        public static function setSession($session)
        {

            return $instance->setSession($session);
        }

        public static function setSessionFactory($factory)
        {

            return $instance->setSessionFactory($factory);
        }

        public static function getClientIps()
        {

            return $instance->getClientIps();
        }

        public static function getClientIp()
        {

            return $instance->getClientIp();
        }

        public static function getScriptName()
        {

            return $instance->getScriptName();
        }

        public static function getPathInfo()
        {

            return $instance->getPathInfo();
        }

        public static function getBasePath()
        {

            return $instance->getBasePath();
        }

        public static function getBaseUrl()
        {

            return $instance->getBaseUrl();
        }

        public static function getScheme()
        {

            return $instance->getScheme();
        }

        public static function getPort()
        {

            return $instance->getPort();
        }

        public static function getUser()
        {

            return $instance->getUser();
        }

        public static function getPassword()
        {

            return $instance->getPassword();
        }

        public static function getUserInfo()
        {

            return $instance->getUserInfo();
        }

        public static function getHttpHost()
        {

            return $instance->getHttpHost();
        }

        public static function getRequestUri()
        {

            return $instance->getRequestUri();
        }

        public static function getSchemeAndHttpHost()
        {

            return $instance->getSchemeAndHttpHost();
        }

        public static function getUri()
        {

            return $instance->getUri();
        }

        public static function getUriForPath($path)
        {

            return $instance->getUriForPath($path);
        }

        public static function getRelativeUriForPath($path)
        {

            return $instance->getRelativeUriForPath($path);
        }

        public static function getQueryString()
        {

            return $instance->getQueryString();
        }

        public static function isSecure()
        {

            return $instance->isSecure();
        }

        public static function getHost()
        {

            return $instance->getHost();
        }

        public static function setMethod($method)
        {

            return $instance->setMethod($method);
        }

        public static function getMethod()
        {

            return $instance->getMethod();
        }

        public static function getRealMethod()
        {

            return $instance->getRealMethod();
        }

        public static function getMimeType($format)
        {

            return $instance->getMimeType($format);
        }

        public static function getMimeTypes($format)
        {
            
            return \Illuminate\Http\Request::getMimeTypes($format);
        }

        public static function getFormat($mimeType)
        {

            return $instance->getFormat($mimeType);
        }

        public static function setFormat($format, $mimeTypes)
        {

            return $instance->setFormat($format, $mimeTypes);
        }

        public static function getRequestFormat($default = 'html')
        {

            return $instance->getRequestFormat($default);
        }

        public static function setRequestFormat($format)
        {

            return $instance->setRequestFormat($format);
        }

        public static function getContentTypeFormat()
        {

            return $instance->getContentTypeFormat();
        }

        public static function setDefaultLocale($locale)
        {

            return $instance->setDefaultLocale($locale);
        }

        public static function getDefaultLocale()
        {

            return $instance->getDefaultLocale();
        }

        public static function setLocale($locale)
        {

            return $instance->setLocale($locale);
        }

        public static function getLocale()
        {

            return $instance->getLocale();
        }

        public static function isMethod($method)
        {

            return $instance->isMethod($method);
        }

        public static function isMethodSafe()
        {

            return $instance->isMethodSafe();
        }

        public static function isMethodIdempotent()
        {

            return $instance->isMethodIdempotent();
        }

        public static function isMethodCacheable()
        {

            return $instance->isMethodCacheable();
        }

        public static function getProtocolVersion()
        {

            return $instance->getProtocolVersion();
        }

        public static function getContent($asResource = false)
        {

            return $instance->getContent($asResource);
        }

        public static function getPayload()
        {

            return $instance->getPayload();
        }

        public static function getETags()
        {

            return $instance->getETags();
        }

        public static function isNoCache()
        {

            return $instance->isNoCache();
        }

        public static function getPreferredFormat($default = 'html')
        {

            return $instance->getPreferredFormat($default);
        }

        public static function getPreferredLanguage($locales = null)
        {

            return $instance->getPreferredLanguage($locales);
        }

        public static function getLanguages()
        {

            return $instance->getLanguages();
        }

        public static function getCharsets()
        {

            return $instance->getCharsets();
        }

        public static function getEncodings()
        {

            return $instance->getEncodings();
        }

        public static function isXmlHttpRequest()
        {

            return $instance->isXmlHttpRequest();
        }

        public static function preferSafeContent()
        {

            return $instance->preferSafeContent();
        }

        public static function isFromTrustedProxy()
        {

            return $instance->isFromTrustedProxy();
        }

        public static function filterPrecognitiveRules($rules)
        {
            
            return $instance->filterPrecognitiveRules($rules);
        }

        public static function isAttemptingPrecognition()
        {
            
            return $instance->isAttemptingPrecognition();
        }

        public static function isPrecognitive()
        {
            
            return $instance->isPrecognitive();
        }

        public static function isJson()
        {
            
            return $instance->isJson();
        }

        public static function expectsJson()
        {
            
            return $instance->expectsJson();
        }

        public static function wantsJson()
        {
            
            return $instance->wantsJson();
        }

        public static function accepts($contentTypes)
        {
            
            return $instance->accepts($contentTypes);
        }

        public static function prefers($contentTypes)
        {
            
            return $instance->prefers($contentTypes);
        }

        public static function acceptsAnyContentType()
        {
            
            return $instance->acceptsAnyContentType();
        }

        public static function acceptsJson()
        {
            
            return $instance->acceptsJson();
        }

        public static function acceptsHtml()
        {
            
            return $instance->acceptsHtml();
        }

        public static function matchesType($actual, $type)
        {
            return \Illuminate\Http\Request::matchesType($actual, $type);
        }

        public static function format($default = 'html')
        {
            
            return $instance->format($default);
        }

        public static function old($key = null, $default = null)
        {
            
            return $instance->old($key, $default);
        }

        public static function flash()
        {
            
            $instance->flash();
        }

        public static function flashOnly($keys)
        {
            
            $instance->flashOnly($keys);
        }

        public static function flashExcept($keys)
        {
            
            $instance->flashExcept($keys);
        }

        public static function flush()
        {
            
            $instance->flush();
        }

        public static function server($key = null, $default = null)
        {
            
            return $instance->server($key, $default);
        }

        public static function hasHeader($key)
        {
            
            return $instance->hasHeader($key);
        }

        public static function header($key = null, $default = null)
        {
            
            return $instance->header($key, $default);
        }

        public static function bearerToken()
        {
            
            return $instance->bearerToken();
        }

        public static function keys()
        {
            
            return $instance->keys();
        }

        public static function all($keys = null)
        {
            
            return $instance->all($keys);
        }

        public static function input($key = null, $default = null)
        {
            
            return $instance->input($key, $default);
        }

        public static function fluent($key = null, $default = [])
        {
            
            return $instance->fluent($key, $default);
        }

        public static function query($key = null, $default = null)
        {
            
            return $instance->query($key, $default);
        }

        public static function post($key = null, $default = null)
        {
            
            return $instance->post($key, $default);
        }

        public static function hasCookie($key)
        {
            
            return $instance->hasCookie($key);
        }

        public static function cookie($key = null, $default = null)
        {
            
            return $instance->cookie($key, $default);
        }

        public static function allFiles()
        {
            
            return $instance->allFiles();
        }

        public static function hasFile($key)
        {
            
            return $instance->hasFile($key);
        }

        public static function file($key = null, $default = null)
        {
            
            return $instance->file($key, $default);
        }

        public static function dump($keys = [])
        {
            
            return $instance->dump($keys);
        }

        public static function dd(...$args)
        {
            
            return $instance->dd(...$args);
        }

        public static function exists($key)
        {
            
            return $instance->exists($key);
        }

        public static function has($key)
        {
            
            return $instance->has($key);
        }

        public static function hasAny($keys)
        {
            
            return $instance->hasAny($keys);
        }

        public static function whenHas($key, $callback, $default = null)
        {
            
            return $instance->whenHas($key, $callback, $default);
        }

        public static function filled($key)
        {
            
            return $instance->filled($key);
        }

        public static function isNotFilled($key)
        {
            
            return $instance->isNotFilled($key);
        }

        public static function anyFilled($keys)
        {
            
            return $instance->anyFilled($keys);
        }

        public static function whenFilled($key, $callback, $default = null)
        {
            
            return $instance->whenFilled($key, $callback, $default);
        }

        public static function missing($key)
        {
            
            return $instance->missing($key);
        }

        public static function whenMissing($key, $callback, $default = null)
        {
            
            return $instance->whenMissing($key, $callback, $default);
        }

        public static function str($key, $default = null)
        {
            
            return $instance->str($key, $default);
        }

        public static function string($key, $default = null)
        {
            
            return $instance->string($key, $default);
        }

        public static function boolean($key = null, $default = false)
        {
            
            return $instance->boolean($key, $default);
        }

        public static function integer($key, $default = 0)
        {
            
            return $instance->integer($key, $default);
        }

        public static function float($key, $default = 0.0)
        {
            
            return $instance->float($key, $default);
        }

        public static function date($key, $format = null, $tz = null)
        {
            
            return $instance->date($key, $format, $tz);
        }

        public static function enum($key, $enumClass, $default = null)
        {
            
            return $instance->enum($key, $enumClass, $default);
        }

        public static function enums($key, $enumClass)
        {
            
            return $instance->enums($key, $enumClass);
        }

        public static function array($key = null)
        {
            
            return $instance->array($key);
        }

        public static function collect($key = null)
        {
            
            return $instance->collect($key);
        }

        public static function only($keys)
        {
            
            return $instance->only($keys);
        }

        public static function except($keys)
        {
            
            return $instance->except($keys);
        }

        public static function when($value = null, $callback = null, $default = null)
        {
            
            return $instance->when($value, $callback, $default);
        }

        public static function unless($value = null, $callback = null, $default = null)
        {
            
            return $instance->unless($value, $callback, $default);
        }

        public static function macro($name, $macro)
        {
            \Illuminate\Http\Request::macro($name, $macro);
        }

        public static function mixin($mixin, $replace = true)
        {
            \Illuminate\Http\Request::mixin($mixin, $replace);
        }

        public static function hasMacro($name)
        {
            return \Illuminate\Http\Request::hasMacro($name);
        }

        public static function flushMacros()
        {
            \Illuminate\Http\Request::flushMacros();
        }

        public static function validate($rules, ...$params)
        {
            return \Illuminate\Http\Request::validate($rules, ...$params);
        }

        public static function validateWithBag($errorBag, $rules, ...$params)
        {
            return \Illuminate\Http\Request::validateWithBag($errorBag, $rules, ...$params);
        }

        public static function hasValidSignature($absolute = true)
        {
            return \Illuminate\Http\Request::hasValidSignature($absolute);
        }

        public static function hasValidRelativeSignature()
        {
            return \Illuminate\Http\Request::hasValidRelativeSignature();
        }

        public static function hasValidSignatureWhileIgnoring($ignoreQuery = [], $absolute = true)
        {
            return \Illuminate\Http\Request::hasValidSignatureWhileIgnoring($ignoreQuery, $absolute);
        }

        public static function hasValidRelativeSignatureWhileIgnoring($ignoreQuery = [])
        {
            return \Illuminate\Http\Request::hasValidRelativeSignatureWhileIgnoring($ignoreQuery);
        }

            }
    
    class Response {
        
        public static function make($content = '', $status = 200, $headers = [])
        {
            
            return $instance->make($content, $status, $headers);
        }

        public static function noContent($status = 204, $headers = [])
        {
            
            return $instance->noContent($status, $headers);
        }

        public static function view($view, $data = [], $status = 200, $headers = [])
        {
            
            return $instance->view($view, $data, $status, $headers);
        }

        public static function json($data = [], $status = 200, $headers = [], $options = 0)
        {
            
            return $instance->json($data, $status, $headers, $options);
        }

        public static function jsonp($callback, $data = [], $status = 200, $headers = [], $options = 0)
        {
            
            return $instance->jsonp($callback, $data, $status, $headers, $options);
        }

        public static function eventStream($callback, $headers = [], $endStreamWith = '</stream>')
        {
            
            return $instance->eventStream($callback, $headers, $endStreamWith);
        }

        public static function stream($callback, $status = 200, $headers = [])
        {
            
            return $instance->stream($callback, $status, $headers);
        }

        public static function streamJson($data, $status = 200, $headers = [], $encodingOptions = 15)
        {
            
            return $instance->streamJson($data, $status, $headers, $encodingOptions);
        }

        public static function streamDownload($callback, $name = null, $headers = [], $disposition = 'attachment')
        {
            
            return $instance->streamDownload($callback, $name, $headers, $disposition);
        }

        public static function download($file, $name = null, $headers = [], $disposition = 'attachment')
        {
            
            return $instance->download($file, $name, $headers, $disposition);
        }

        public static function file($file, $headers = [])
        {
            
            return $instance->file($file, $headers);
        }

        public static function redirectTo($path, $status = 302, $headers = [], $secure = null)
        {
            
            return $instance->redirectTo($path, $status, $headers, $secure);
        }

        public static function redirectToRoute($route, $parameters = [], $status = 302, $headers = [])
        {
            
            return $instance->redirectToRoute($route, $parameters, $status, $headers);
        }

        public static function redirectToAction($action, $parameters = [], $status = 302, $headers = [])
        {
            
            return $instance->redirectToAction($action, $parameters, $status, $headers);
        }

        public static function redirectGuest($path, $status = 302, $headers = [], $secure = null)
        {
            
            return $instance->redirectGuest($path, $status, $headers, $secure);
        }

        public static function redirectToIntended($default = '/', $status = 302, $headers = [], $secure = null)
        {
            
            return $instance->redirectToIntended($default, $status, $headers, $secure);
        }

        public static function macro($name, $macro)
        {
            \Illuminate\Routing\ResponseFactory::macro($name, $macro);
        }

        public static function mixin($mixin, $replace = true)
        {
            \Illuminate\Routing\ResponseFactory::mixin($mixin, $replace);
        }

        public static function hasMacro($name)
        {
            return \Illuminate\Routing\ResponseFactory::hasMacro($name);
        }

        public static function flushMacros()
        {
            \Illuminate\Routing\ResponseFactory::flushMacros();
        }

            }
    
    class Route {
        
        public static function get($uri, $action = null)
        {
            
            return $instance->get($uri, $action);
        }

        public static function post($uri, $action = null)
        {
            
            return $instance->post($uri, $action);
        }

        public static function put($uri, $action = null)
        {
            
            return $instance->put($uri, $action);
        }

        public static function patch($uri, $action = null)
        {
            
            return $instance->patch($uri, $action);
        }

        public static function delete($uri, $action = null)
        {
            
            return $instance->delete($uri, $action);
        }

        public static function options($uri, $action = null)
        {
            
            return $instance->options($uri, $action);
        }

        public static function any($uri, $action = null)
        {
            
            return $instance->any($uri, $action);
        }

        public static function fallback($action)
        {
            
            return $instance->fallback($action);
        }

        public static function redirect($uri, $destination, $status = 302)
        {
            
            return $instance->redirect($uri, $destination, $status);
        }

        public static function permanentRedirect($uri, $destination)
        {
            
            return $instance->permanentRedirect($uri, $destination);
        }

        public static function view($uri, $view, $data = [], $status = 200, $headers = [])
        {
            
            return $instance->view($uri, $view, $data, $status, $headers);
        }

        public static function match($methods, $uri, $action = null)
        {
            
            return $instance->match($methods, $uri, $action);
        }

        public static function resources($resources, $options = [])
        {
            
            $instance->resources($resources, $options);
        }

        public static function softDeletableResources($resources, $options = [])
        {
            
            $instance->softDeletableResources($resources, $options);
        }

        public static function resource($name, $controller, $options = [])
        {
            
            return $instance->resource($name, $controller, $options);
        }

        public static function apiResources($resources, $options = [])
        {
            
            $instance->apiResources($resources, $options);
        }

        public static function apiResource($name, $controller, $options = [])
        {
            
            return $instance->apiResource($name, $controller, $options);
        }

        public static function singletons($singletons, $options = [])
        {
            
            $instance->singletons($singletons, $options);
        }

        public static function singleton($name, $controller, $options = [])
        {
            
            return $instance->singleton($name, $controller, $options);
        }

        public static function apiSingletons($singletons, $options = [])
        {
            
            $instance->apiSingletons($singletons, $options);
        }

        public static function apiSingleton($name, $controller, $options = [])
        {
            
            return $instance->apiSingleton($name, $controller, $options);
        }

        public static function group($attributes, $routes)
        {
            
            return $instance->group($attributes, $routes);
        }

        public static function mergeWithLastGroup($new, $prependExistingPrefix = true)
        {
            
            return $instance->mergeWithLastGroup($new, $prependExistingPrefix);
        }

        public static function getLastGroupPrefix()
        {
            
            return $instance->getLastGroupPrefix();
        }

        public static function addRoute($methods, $uri, $action)
        {
            
            return $instance->addRoute($methods, $uri, $action);
        }

        public static function newRoute($methods, $uri, $action)
        {
            
            return $instance->newRoute($methods, $uri, $action);
        }

        public static function respondWithRoute($name)
        {
            
            return $instance->respondWithRoute($name);
        }

        public static function dispatch($request)
        {
            
            return $instance->dispatch($request);
        }

        public static function dispatchToRoute($request)
        {
            
            return $instance->dispatchToRoute($request);
        }

        public static function gatherRouteMiddleware($route)
        {
            
            return $instance->gatherRouteMiddleware($route);
        }

        public static function resolveMiddleware($middleware, $excluded = [])
        {
            
            return $instance->resolveMiddleware($middleware, $excluded);
        }

        public static function prepareResponse($request, $response)
        {
            
            return $instance->prepareResponse($request, $response);
        }

        public static function toResponse($request, $response)
        {
            return \Illuminate\Routing\Router::toResponse($request, $response);
        }

        public static function substituteBindings($route)
        {
            
            return $instance->substituteBindings($route);
        }

        public static function substituteImplicitBindings($route)
        {
            
            $instance->substituteImplicitBindings($route);
        }

        public static function substituteImplicitBindingsUsing($callback)
        {
            
            return $instance->substituteImplicitBindingsUsing($callback);
        }

        public static function matched($callback)
        {
            
            $instance->matched($callback);
        }

        public static function getMiddleware()
        {
            
            return $instance->getMiddleware();
        }

        public static function aliasMiddleware($name, $class)
        {
            
            return $instance->aliasMiddleware($name, $class);
        }

        public static function hasMiddlewareGroup($name)
        {
            
            return $instance->hasMiddlewareGroup($name);
        }

        public static function getMiddlewareGroups()
        {
            
            return $instance->getMiddlewareGroups();
        }

        public static function middlewareGroup($name, $middleware)
        {
            
            return $instance->middlewareGroup($name, $middleware);
        }

        public static function prependMiddlewareToGroup($group, $middleware)
        {
            
            return $instance->prependMiddlewareToGroup($group, $middleware);
        }

        public static function pushMiddlewareToGroup($group, $middleware)
        {
            
            return $instance->pushMiddlewareToGroup($group, $middleware);
        }

        public static function removeMiddlewareFromGroup($group, $middleware)
        {
            
            return $instance->removeMiddlewareFromGroup($group, $middleware);
        }

        public static function flushMiddlewareGroups()
        {
            
            return $instance->flushMiddlewareGroups();
        }

        public static function bind($key, $binder)
        {
            
            $instance->bind($key, $binder);
        }

        public static function model($key, $class, $callback = null)
        {
            
            $instance->model($key, $class, $callback);
        }

        public static function getBindingCallback($key)
        {
            
            return $instance->getBindingCallback($key);
        }

        public static function getPatterns()
        {
            
            return $instance->getPatterns();
        }

        public static function pattern($key, $pattern)
        {
            
            $instance->pattern($key, $pattern);
        }

        public static function patterns($patterns)
        {
            
            $instance->patterns($patterns);
        }

        public static function hasGroupStack()
        {
            
            return $instance->hasGroupStack();
        }

        public static function getGroupStack()
        {
            
            return $instance->getGroupStack();
        }

        public static function input($key, $default = null)
        {
            
            return $instance->input($key, $default);
        }

        public static function getCurrentRequest()
        {
            
            return $instance->getCurrentRequest();
        }

        public static function getCurrentRoute()
        {
            
            return $instance->getCurrentRoute();
        }

        public static function current()
        {
            
            return $instance->current();
        }

        public static function has($name)
        {
            
            return $instance->has($name);
        }

        public static function currentRouteName()
        {
            
            return $instance->currentRouteName();
        }

        public static function is(...$patterns)
        {
            
            return $instance->is(...$patterns);
        }

        public static function currentRouteNamed(...$patterns)
        {
            
            return $instance->currentRouteNamed(...$patterns);
        }

        public static function currentRouteAction()
        {
            
            return $instance->currentRouteAction();
        }

        public static function uses(...$patterns)
        {
            
            return $instance->uses(...$patterns);
        }

        public static function currentRouteUses($action)
        {
            
            return $instance->currentRouteUses($action);
        }

        public static function singularResourceParameters($singular = true)
        {
            
            $instance->singularResourceParameters($singular);
        }

        public static function resourceParameters($parameters = [])
        {
            
            $instance->resourceParameters($parameters);
        }

        public static function resourceVerbs($verbs = [])
        {
            
            return $instance->resourceVerbs($verbs);
        }

        public static function getRoutes()
        {
            
            return $instance->getRoutes();
        }

        public static function setRoutes($routes)
        {
            
            $instance->setRoutes($routes);
        }

        public static function setCompiledRoutes($routes)
        {
            
            $instance->setCompiledRoutes($routes);
        }

        public static function uniqueMiddleware($middleware)
        {
            return \Illuminate\Routing\Router::uniqueMiddleware($middleware);
        }

        public static function setContainer($container)
        {
            
            return $instance->setContainer($container);
        }

        public static function macro($name, $macro)
        {
            \Illuminate\Routing\Router::macro($name, $macro);
        }

        public static function mixin($mixin, $replace = true)
        {
            \Illuminate\Routing\Router::mixin($mixin, $replace);
        }

        public static function hasMacro($name)
        {
            return \Illuminate\Routing\Router::hasMacro($name);
        }

        public static function flushMacros()
        {
            \Illuminate\Routing\Router::flushMacros();
        }

        public static function macroCall($method, $parameters)
        {
            
            return $instance->macroCall($method, $parameters);
        }

        public static function tap($callback = null)
        {
            
            return $instance->tap($callback);
        }

            }
    
    class Schedule {
        
        public static function call($callback, $parameters = [])
        {
            
            return $instance->call($callback, $parameters);
        }

        public static function command($command, $parameters = [])
        {
            
            return $instance->command($command, $parameters);
        }

        public static function job($job, $queue = null, $connection = null)
        {
            
            return $instance->job($job, $queue, $connection);
        }

        public static function exec($command, $parameters = [])
        {
            
            return $instance->exec($command, $parameters);
        }

        public static function group($events)
        {
            
            $instance->group($events);
        }

        public static function compileArrayInput($key, $value)
        {
            
            return $instance->compileArrayInput($key, $value);
        }

        public static function serverShouldRun($event, $time)
        {
            
            return $instance->serverShouldRun($event, $time);
        }

        public static function dueEvents($app)
        {
            
            return $instance->dueEvents($app);
        }

        public static function events()
        {
            
            return $instance->events();
        }

        public static function useCache($store)
        {
            
            return $instance->useCache($store);
        }

        public static function macro($name, $macro)
        {
            \Illuminate\Console\Scheduling\Schedule::macro($name, $macro);
        }

        public static function mixin($mixin, $replace = true)
        {
            \Illuminate\Console\Scheduling\Schedule::mixin($mixin, $replace);
        }

        public static function hasMacro($name)
        {
            return \Illuminate\Console\Scheduling\Schedule::hasMacro($name);
        }

        public static function flushMacros()
        {
            \Illuminate\Console\Scheduling\Schedule::flushMacros();
        }

        public static function macroCall($method, $parameters)
        {
            
            return $instance->macroCall($method, $parameters);
        }

            }
    
    class Schema {
        
        public static function dropAllTables()
        {
            
            $instance->dropAllTables();
        }

        public static function dropAllViews()
        {
            
            $instance->dropAllViews();
        }

        public static function dropAllTypes()
        {
            
            $instance->dropAllTypes();
        }

        public static function getCurrentSchemaListing()
        {
            
            return $instance->getCurrentSchemaListing();
        }

        public static function defaultStringLength($length)
        {
            
            \Illuminate\Database\Schema\PostgresBuilder::defaultStringLength($length);
        }

        public static function defaultTimePrecision($precision)
        {
            
            return \Illuminate\Database\Schema\PostgresBuilder::defaultTimePrecision($precision);
        }

        public static function defaultMorphKeyType($type)
        {
            
            \Illuminate\Database\Schema\PostgresBuilder::defaultMorphKeyType($type);
        }

        public static function morphUsingUuids()
        {
            
            \Illuminate\Database\Schema\PostgresBuilder::morphUsingUuids();
        }

        public static function morphUsingUlids()
        {
            
            \Illuminate\Database\Schema\PostgresBuilder::morphUsingUlids();
        }

        public static function createDatabase($name)
        {

            return $instance->createDatabase($name);
        }

        public static function dropDatabaseIfExists($name)
        {

            return $instance->dropDatabaseIfExists($name);
        }

        public static function getSchemas()
        {

            return $instance->getSchemas();
        }

        public static function hasTable($table)
        {

            return $instance->hasTable($table);
        }

        public static function hasView($view)
        {

            return $instance->hasView($view);
        }

        public static function getTables($schema = null)
        {

            return $instance->getTables($schema);
        }

        public static function getTableListing($schema = null, $schemaQualified = true)
        {

            return $instance->getTableListing($schema, $schemaQualified);
        }

        public static function getViews($schema = null)
        {

            return $instance->getViews($schema);
        }

        public static function getTypes($schema = null)
        {

            return $instance->getTypes($schema);
        }

        public static function hasColumn($table, $column)
        {

            return $instance->hasColumn($table, $column);
        }

        public static function hasColumns($table, $columns)
        {

            return $instance->hasColumns($table, $columns);
        }

        public static function whenTableHasColumn($table, $column, $callback)
        {

            $instance->whenTableHasColumn($table, $column, $callback);
        }

        public static function whenTableDoesntHaveColumn($table, $column, $callback)
        {

            $instance->whenTableDoesntHaveColumn($table, $column, $callback);
        }

        public static function whenTableHasIndex($table, $index, $callback, $type = null)
        {

            $instance->whenTableHasIndex($table, $index, $callback, $type);
        }

        public static function whenTableDoesntHaveIndex($table, $index, $callback, $type = null)
        {

            $instance->whenTableDoesntHaveIndex($table, $index, $callback, $type);
        }

        public static function getColumnType($table, $column, $fullDefinition = false)
        {

            return $instance->getColumnType($table, $column, $fullDefinition);
        }

        public static function getColumnListing($table)
        {

            return $instance->getColumnListing($table);
        }

        public static function getColumns($table)
        {

            return $instance->getColumns($table);
        }

        public static function getIndexes($table)
        {

            return $instance->getIndexes($table);
        }

        public static function getIndexListing($table)
        {

            return $instance->getIndexListing($table);
        }

        public static function hasIndex($table, $index, $type = null)
        {

            return $instance->hasIndex($table, $index, $type);
        }

        public static function getForeignKeys($table)
        {

            return $instance->getForeignKeys($table);
        }

        public static function table($table, $callback)
        {

            $instance->table($table, $callback);
        }

        public static function create($table, $callback)
        {

            $instance->create($table, $callback);
        }

        public static function drop($table)
        {

            $instance->drop($table);
        }

        public static function dropIfExists($table)
        {

            $instance->dropIfExists($table);
        }

        public static function dropColumns($table, $columns)
        {

            $instance->dropColumns($table, $columns);
        }

        public static function rename($from, $to)
        {

            $instance->rename($from, $to);
        }

        public static function enableForeignKeyConstraints()
        {

            return $instance->enableForeignKeyConstraints();
        }

        public static function disableForeignKeyConstraints()
        {

            return $instance->disableForeignKeyConstraints();
        }

        public static function withoutForeignKeyConstraints($callback)
        {

            return $instance->withoutForeignKeyConstraints($callback);
        }

        public static function ensureVectorExtensionExists($schema = null)
        {

            $instance->ensureVectorExtensionExists($schema);
        }

        public static function ensureExtensionExists($name, $schema = null)
        {

            $instance->ensureExtensionExists($name, $schema);
        }

        public static function getCurrentSchemaName()
        {

            return $instance->getCurrentSchemaName();
        }

        public static function parseSchemaAndTable($reference, $withDefaultSchema = null)
        {

            return $instance->parseSchemaAndTable($reference, $withDefaultSchema);
        }

        public static function getConnection()
        {

            return $instance->getConnection();
        }

        public static function blueprintResolver($resolver)
        {

            $instance->blueprintResolver($resolver);
        }

        public static function macro($name, $macro)
        {
            
            \Illuminate\Database\Schema\PostgresBuilder::macro($name, $macro);
        }

        public static function mixin($mixin, $replace = true)
        {
            
            \Illuminate\Database\Schema\PostgresBuilder::mixin($mixin, $replace);
        }

        public static function hasMacro($name)
        {
            
            return \Illuminate\Database\Schema\PostgresBuilder::hasMacro($name);
        }

        public static function flushMacros()
        {
            
            \Illuminate\Database\Schema\PostgresBuilder::flushMacros();
        }

            }
    
    class Session {
        
        public static function shouldBlock()
        {
            
            return $instance->shouldBlock();
        }

        public static function blockDriver()
        {
            
            return $instance->blockDriver();
        }

        public static function defaultRouteBlockLockSeconds()
        {
            
            return $instance->defaultRouteBlockLockSeconds();
        }

        public static function defaultRouteBlockWaitSeconds()
        {
            
            return $instance->defaultRouteBlockWaitSeconds();
        }

        public static function getSessionConfig()
        {
            
            return $instance->getSessionConfig();
        }

        public static function getDefaultDriver()
        {
            
            return $instance->getDefaultDriver();
        }

        public static function setDefaultDriver($name)
        {
            
            $instance->setDefaultDriver($name);
        }

        public static function driver($driver = null)
        {

            return $instance->driver($driver);
        }

        public static function extend($driver, $callback)
        {

            return $instance->extend($driver, $callback);
        }

        public static function getDrivers()
        {

            return $instance->getDrivers();
        }

        public static function getContainer()
        {

            return $instance->getContainer();
        }

        public static function setContainer($container)
        {

            return $instance->setContainer($container);
        }

        public static function forgetDrivers()
        {

            return $instance->forgetDrivers();
        }

        public static function start()
        {
            
            return $instance->start();
        }

        public static function save()
        {
            
            $instance->save();
        }

        public static function ageFlashData()
        {
            
            $instance->ageFlashData();
        }

        public static function all()
        {
            
            return $instance->all();
        }

        public static function only($keys)
        {
            
            return $instance->only($keys);
        }

        public static function except($keys)
        {
            
            return $instance->except($keys);
        }

        public static function exists($key)
        {
            
            return $instance->exists($key);
        }

        public static function missing($key)
        {
            
            return $instance->missing($key);
        }

        public static function has($key)
        {
            
            return $instance->has($key);
        }

        public static function hasAny($key)
        {
            
            return $instance->hasAny($key);
        }

        public static function get($key, $default = null)
        {
            
            return $instance->get($key, $default);
        }

        public static function pull($key, $default = null)
        {
            
            return $instance->pull($key, $default);
        }

        public static function hasOldInput($key = null)
        {
            
            return $instance->hasOldInput($key);
        }

        public static function getOldInput($key = null, $default = null)
        {
            
            return $instance->getOldInput($key, $default);
        }

        public static function replace($attributes)
        {
            
            $instance->replace($attributes);
        }

        public static function put($key, $value = null)
        {
            
            $instance->put($key, $value);
        }

        public static function remember($key, $callback)
        {
            
            return $instance->remember($key, $callback);
        }

        public static function push($key, $value)
        {
            
            $instance->push($key, $value);
        }

        public static function increment($key, $amount = 1)
        {
            
            return $instance->increment($key, $amount);
        }

        public static function decrement($key, $amount = 1)
        {
            
            return $instance->decrement($key, $amount);
        }

        public static function flash($key, $value = true)
        {
            
            $instance->flash($key, $value);
        }

        public static function now($key, $value)
        {
            
            $instance->now($key, $value);
        }

        public static function reflash()
        {
            
            $instance->reflash();
        }

        public static function keep($keys = null)
        {
            
            $instance->keep($keys);
        }

        public static function flashInput($value)
        {
            
            $instance->flashInput($value);
        }

        public static function cache()
        {
            
            return $instance->cache();
        }

        public static function remove($key)
        {
            
            return $instance->remove($key);
        }

        public static function forget($keys)
        {
            
            $instance->forget($keys);
        }

        public static function flush()
        {
            
            $instance->flush();
        }

        public static function invalidate()
        {
            
            return $instance->invalidate();
        }

        public static function regenerate($destroy = false)
        {
            
            return $instance->regenerate($destroy);
        }

        public static function migrate($destroy = false)
        {
            
            return $instance->migrate($destroy);
        }

        public static function isStarted()
        {
            
            return $instance->isStarted();
        }

        public static function getName()
        {
            
            return $instance->getName();
        }

        public static function setName($name)
        {
            
            $instance->setName($name);
        }

        public static function id()
        {
            
            return $instance->id();
        }

        public static function getId()
        {
            
            return $instance->getId();
        }

        public static function setId($id)
        {
            
            $instance->setId($id);
        }

        public static function isValidId($id)
        {
            
            return $instance->isValidId($id);
        }

        public static function setExists($value)
        {
            
            $instance->setExists($value);
        }

        public static function token()
        {
            
            return $instance->token();
        }

        public static function regenerateToken()
        {
            
            $instance->regenerateToken();
        }

        public static function hasPreviousUri()
        {
            
            return $instance->hasPreviousUri();
        }

        public static function previousUri()
        {
            
            return $instance->previousUri();
        }

        public static function previousUrl()
        {
            
            return $instance->previousUrl();
        }

        public static function setPreviousUrl($url)
        {
            
            $instance->setPreviousUrl($url);
        }

        public static function previousRoute()
        {
            
            return $instance->previousRoute();
        }

        public static function setPreviousRoute($route)
        {
            
            $instance->setPreviousRoute($route);
        }

        public static function passwordConfirmed()
        {
            
            $instance->passwordConfirmed();
        }

        public static function getHandler()
        {
            
            return $instance->getHandler();
        }

        public static function setHandler($handler)
        {
            
            return $instance->setHandler($handler);
        }

        public static function handlerNeedsRequest()
        {
            
            return $instance->handlerNeedsRequest();
        }

        public static function setRequestOnHandler($request)
        {
            
            $instance->setRequestOnHandler($request);
        }

        public static function macro($name, $macro)
        {
            \Illuminate\Session\Store::macro($name, $macro);
        }

        public static function mixin($mixin, $replace = true)
        {
            \Illuminate\Session\Store::mixin($mixin, $replace);
        }

        public static function hasMacro($name)
        {
            return \Illuminate\Session\Store::hasMacro($name);
        }

        public static function flushMacros()
        {
            \Illuminate\Session\Store::flushMacros();
        }

            }
    
    class Storage {
        
        public static function drive($name = null)
        {
            
            return $instance->drive($name);
        }

        public static function disk($name = null)
        {
            
            return $instance->disk($name);
        }

        public static function cloud()
        {
            
            return $instance->cloud();
        }

        public static function build($config)
        {
            
            return $instance->build($config);
        }

        public static function createLocalDriver($config, $name = 'local')
        {
            
            return $instance->createLocalDriver($config, $name);
        }

        public static function createFtpDriver($config)
        {
            
            return $instance->createFtpDriver($config);
        }

        public static function createSftpDriver($config)
        {
            
            return $instance->createSftpDriver($config);
        }

        public static function createS3Driver($config)
        {
            
            return $instance->createS3Driver($config);
        }

        public static function createScopedDriver($config)
        {
            
            return $instance->createScopedDriver($config);
        }

        public static function set($name, $disk)
        {
            
            return $instance->set($name, $disk);
        }

        public static function getDefaultDriver()
        {
            
            return $instance->getDefaultDriver();
        }

        public static function getDefaultCloudDriver()
        {
            
            return $instance->getDefaultCloudDriver();
        }

        public static function forgetDisk($disk)
        {
            
            return $instance->forgetDisk($disk);
        }

        public static function purge($name = null)
        {
            
            $instance->purge($name);
        }

        public static function extend($driver, $callback)
        {
            
            return $instance->extend($driver, $callback);
        }

        public static function setApplication($app)
        {
            
            return $instance->setApplication($app);
        }

        public static function providesTemporaryUrls()
        {
            
            return $instance->providesTemporaryUrls();
        }

        public static function temporaryUrl($path, $expiration, $options = [])
        {
            
            return $instance->temporaryUrl($path, $expiration, $options);
        }

        public static function diskName($disk)
        {
            
            return $instance->diskName($disk);
        }

        public static function shouldServeSignedUrls($serve = true, $urlGeneratorResolver = null)
        {
            
            return $instance->shouldServeSignedUrls($serve, $urlGeneratorResolver);
        }

        public static function assertExists($path, $content = null)
        {

            return $instance->assertExists($path, $content);
        }

        public static function assertCount($path, $count, $recursive = false)
        {

            return $instance->assertCount($path, $count, $recursive);
        }

        public static function assertMissing($path)
        {

            return $instance->assertMissing($path);
        }

        public static function assertDirectoryEmpty($path)
        {

            return $instance->assertDirectoryEmpty($path);
        }

        public static function exists($path)
        {

            return $instance->exists($path);
        }

        public static function missing($path)
        {

            return $instance->missing($path);
        }

        public static function fileExists($path)
        {

            return $instance->fileExists($path);
        }

        public static function fileMissing($path)
        {

            return $instance->fileMissing($path);
        }

        public static function directoryExists($path)
        {

            return $instance->directoryExists($path);
        }

        public static function directoryMissing($path)
        {

            return $instance->directoryMissing($path);
        }

        public static function path($path)
        {

            return $instance->path($path);
        }

        public static function get($path)
        {

            return $instance->get($path);
        }

        public static function json($path, $flags = 0)
        {

            return $instance->json($path, $flags);
        }

        public static function response($path, $name = null, $headers = [], $disposition = 'inline')
        {

            return $instance->response($path, $name, $headers, $disposition);
        }

        public static function serve($request, $path, $name = null, $headers = [])
        {

            return $instance->serve($request, $path, $name, $headers);
        }

        public static function download($path, $name = null, $headers = [])
        {

            return $instance->download($path, $name, $headers);
        }

        public static function put($path, $contents, $options = [])
        {

            return $instance->put($path, $contents, $options);
        }

        public static function putFile($path, $file = null, $options = [])
        {

            return $instance->putFile($path, $file, $options);
        }

        public static function putFileAs($path, $file, $name = null, $options = [])
        {

            return $instance->putFileAs($path, $file, $name, $options);
        }

        public static function getVisibility($path)
        {

            return $instance->getVisibility($path);
        }

        public static function setVisibility($path, $visibility)
        {

            return $instance->setVisibility($path, $visibility);
        }

        public static function prepend($path, $data, $separator = '
')
        {

            return $instance->prepend($path, $data, $separator);
        }

        public static function append($path, $data, $separator = '
')
        {

            return $instance->append($path, $data, $separator);
        }

        public static function delete($paths)
        {

            return $instance->delete($paths);
        }

        public static function copy($from, $to)
        {

            return $instance->copy($from, $to);
        }

        public static function move($from, $to)
        {

            return $instance->move($from, $to);
        }

        public static function size($path)
        {

            return $instance->size($path);
        }

        public static function checksum($path, $options = [])
        {

            return $instance->checksum($path, $options);
        }

        public static function mimeType($path)
        {

            return $instance->mimeType($path);
        }

        public static function lastModified($path)
        {

            return $instance->lastModified($path);
        }

        public static function readStream($path)
        {

            return $instance->readStream($path);
        }

        public static function writeStream($path, $resource, $options = [])
        {

            return $instance->writeStream($path, $resource, $options);
        }

        public static function url($path)
        {

            return $instance->url($path);
        }

        public static function temporaryUploadUrl($path, $expiration, $options = [])
        {

            return $instance->temporaryUploadUrl($path, $expiration, $options);
        }

        public static function files($directory = null, $recursive = false)
        {

            return $instance->files($directory, $recursive);
        }

        public static function allFiles($directory = null)
        {

            return $instance->allFiles($directory);
        }

        public static function directories($directory = null, $recursive = false)
        {

            return $instance->directories($directory, $recursive);
        }

        public static function allDirectories($directory = null)
        {

            return $instance->allDirectories($directory);
        }

        public static function makeDirectory($path)
        {

            return $instance->makeDirectory($path);
        }

        public static function deleteDirectory($directory)
        {

            return $instance->deleteDirectory($directory);
        }

        public static function getDriver()
        {

            return $instance->getDriver();
        }

        public static function getAdapter()
        {

            return $instance->getAdapter();
        }

        public static function getConfig()
        {

            return $instance->getConfig();
        }

        public static function serveUsing($callback)
        {

            $instance->serveUsing($callback);
        }

        public static function buildTemporaryUrlsUsing($callback)
        {

            $instance->buildTemporaryUrlsUsing($callback);
        }

        public static function when($value = null, $callback = null, $default = null)
        {
            
            return $instance->when($value, $callback, $default);
        }

        public static function unless($value = null, $callback = null, $default = null)
        {
            
            return $instance->unless($value, $callback, $default);
        }

        public static function macro($name, $macro)
        {
            
            \Illuminate\Filesystem\LocalFilesystemAdapter::macro($name, $macro);
        }

        public static function mixin($mixin, $replace = true)
        {
            
            \Illuminate\Filesystem\LocalFilesystemAdapter::mixin($mixin, $replace);
        }

        public static function hasMacro($name)
        {
            
            return \Illuminate\Filesystem\LocalFilesystemAdapter::hasMacro($name);
        }

        public static function flushMacros()
        {
            
            \Illuminate\Filesystem\LocalFilesystemAdapter::flushMacros();
        }

        public static function macroCall($method, $parameters)
        {

            return $instance->macroCall($method, $parameters);
        }

            }
    
    class URL {
        
        public static function full()
        {
            
            return $instance->full();
        }

        public static function current()
        {
            
            return $instance->current();
        }

        public static function previous($fallback = false)
        {
            
            return $instance->previous($fallback);
        }

        public static function previousPath($fallback = false)
        {
            
            return $instance->previousPath($fallback);
        }

        public static function to($path, $extra = [], $secure = null)
        {
            
            return $instance->to($path, $extra, $secure);
        }

        public static function query($path, $query = [], $extra = [], $secure = null)
        {
            
            return $instance->query($path, $query, $extra, $secure);
        }

        public static function secure($path, $parameters = [])
        {
            
            return $instance->secure($path, $parameters);
        }

        public static function asset($path, $secure = null)
        {
            
            return $instance->asset($path, $secure);
        }

        public static function secureAsset($path)
        {
            
            return $instance->secureAsset($path);
        }

        public static function assetFrom($root, $path, $secure = null)
        {
            
            return $instance->assetFrom($root, $path, $secure);
        }

        public static function formatScheme($secure = null)
        {
            
            return $instance->formatScheme($secure);
        }

        public static function signedRoute($name, $parameters = [], $expiration = null, $absolute = true)
        {
            
            return $instance->signedRoute($name, $parameters, $expiration, $absolute);
        }

        public static function temporarySignedRoute($name, $expiration, $parameters = [], $absolute = true)
        {
            
            return $instance->temporarySignedRoute($name, $expiration, $parameters, $absolute);
        }

        public static function hasValidSignature($request, $absolute = true, $ignoreQuery = [])
        {
            
            return $instance->hasValidSignature($request, $absolute, $ignoreQuery);
        }

        public static function hasValidRelativeSignature($request, $ignoreQuery = [])
        {
            
            return $instance->hasValidRelativeSignature($request, $ignoreQuery);
        }

        public static function hasCorrectSignature($request, $absolute = true, $ignoreQuery = [])
        {
            
            return $instance->hasCorrectSignature($request, $absolute, $ignoreQuery);
        }

        public static function signatureHasNotExpired($request)
        {
            
            return $instance->signatureHasNotExpired($request);
        }

        public static function route($name, $parameters = [], $absolute = true)
        {
            
            return $instance->route($name, $parameters, $absolute);
        }

        public static function toRoute($route, $parameters, $absolute)
        {
            
            return $instance->toRoute($route, $parameters, $absolute);
        }

        public static function action($action, $parameters = [], $absolute = true)
        {
            
            return $instance->action($action, $parameters, $absolute);
        }

        public static function formatParameters($parameters)
        {
            
            return $instance->formatParameters($parameters);
        }

        public static function formatRoot($scheme, $root = null)
        {
            
            return $instance->formatRoot($scheme, $root);
        }

        public static function format($root, $path, $route = null)
        {
            
            return $instance->format($root, $path, $route);
        }

        public static function isValidUrl($path)
        {
            
            return $instance->isValidUrl($path);
        }

        public static function defaults($defaults)
        {
            
            $instance->defaults($defaults);
        }

        public static function getDefaultParameters()
        {
            
            return $instance->getDefaultParameters();
        }

        public static function forceScheme($scheme)
        {
            
            $instance->forceScheme($scheme);
        }

        public static function forceHttps($force = true)
        {
            
            $instance->forceHttps($force);
        }

        public static function useOrigin($root)
        {
            
            $instance->useOrigin($root);
        }

        public static function forceRootUrl($root)
        {
            
            $instance->forceRootUrl($root);
        }

        public static function useAssetOrigin($root)
        {
            
            $instance->useAssetOrigin($root);
        }

        public static function formatHostUsing($callback)
        {
            
            return $instance->formatHostUsing($callback);
        }

        public static function formatPathUsing($callback)
        {
            
            return $instance->formatPathUsing($callback);
        }

        public static function pathFormatter()
        {
            
            return $instance->pathFormatter();
        }

        public static function getRequest()
        {
            
            return $instance->getRequest();
        }

        public static function setRequest($request)
        {
            
            $instance->setRequest($request);
        }

        public static function setRoutes($routes)
        {
            
            return $instance->setRoutes($routes);
        }

        public static function setSessionResolver($sessionResolver)
        {
            
            return $instance->setSessionResolver($sessionResolver);
        }

        public static function setKeyResolver($keyResolver)
        {
            
            return $instance->setKeyResolver($keyResolver);
        }

        public static function withKeyResolver($keyResolver)
        {
            
            return $instance->withKeyResolver($keyResolver);
        }

        public static function resolveMissingNamedRoutesUsing($missingNamedRouteResolver)
        {
            
            return $instance->resolveMissingNamedRoutesUsing($missingNamedRouteResolver);
        }

        public static function getRootControllerNamespace()
        {
            
            return $instance->getRootControllerNamespace();
        }

        public static function setRootControllerNamespace($rootNamespace)
        {
            
            return $instance->setRootControllerNamespace($rootNamespace);
        }

        public static function macro($name, $macro)
        {
            \Illuminate\Routing\UrlGenerator::macro($name, $macro);
        }

        public static function mixin($mixin, $replace = true)
        {
            \Illuminate\Routing\UrlGenerator::mixin($mixin, $replace);
        }

        public static function hasMacro($name)
        {
            return \Illuminate\Routing\UrlGenerator::hasMacro($name);
        }

        public static function flushMacros()
        {
            \Illuminate\Routing\UrlGenerator::flushMacros();
        }

            }
    
    class Validator {
        
        public static function make($data, $rules, $messages = [], $attributes = [])
        {
            
            return $instance->make($data, $rules, $messages, $attributes);
        }

        public static function validate($data, $rules, $messages = [], $attributes = [])
        {
            
            return $instance->validate($data, $rules, $messages, $attributes);
        }

        public static function extend($rule, $extension, $message = null)
        {
            
            $instance->extend($rule, $extension, $message);
        }

        public static function extendImplicit($rule, $extension, $message = null)
        {
            
            $instance->extendImplicit($rule, $extension, $message);
        }

        public static function extendDependent($rule, $extension, $message = null)
        {
            
            $instance->extendDependent($rule, $extension, $message);
        }

        public static function replacer($rule, $replacer)
        {
            
            $instance->replacer($rule, $replacer);
        }

        public static function includeUnvalidatedArrayKeys()
        {
            
            $instance->includeUnvalidatedArrayKeys();
        }

        public static function excludeUnvalidatedArrayKeys()
        {
            
            $instance->excludeUnvalidatedArrayKeys();
        }

        public static function resolver($resolver)
        {
            
            $instance->resolver($resolver);
        }

        public static function getTranslator()
        {
            
            return $instance->getTranslator();
        }

        public static function getPresenceVerifier()
        {
            
            return $instance->getPresenceVerifier();
        }

        public static function setPresenceVerifier($presenceVerifier)
        {
            
            $instance->setPresenceVerifier($presenceVerifier);
        }

        public static function getContainer()
        {
            
            return $instance->getContainer();
        }

        public static function setContainer($container)
        {
            
            return $instance->setContainer($container);
        }

            }
    
    class View {
        
        public static function file($path, $data = [], $mergeData = [])
        {
            
            return $instance->file($path, $data, $mergeData);
        }

        public static function make($view, $data = [], $mergeData = [])
        {
            
            return $instance->make($view, $data, $mergeData);
        }

        public static function first($views, $data = [], $mergeData = [])
        {
            
            return $instance->first($views, $data, $mergeData);
        }

        public static function renderWhen($condition, $view, $data = [], $mergeData = [])
        {
            
            return $instance->renderWhen($condition, $view, $data, $mergeData);
        }

        public static function renderUnless($condition, $view, $data = [], $mergeData = [])
        {
            
            return $instance->renderUnless($condition, $view, $data, $mergeData);
        }

        public static function renderEach($view, $data, $iterator, $empty = 'raw|')
        {
            
            return $instance->renderEach($view, $data, $iterator, $empty);
        }

        public static function exists($view)
        {
            
            return $instance->exists($view);
        }

        public static function getEngineFromPath($path)
        {
            
            return $instance->getEngineFromPath($path);
        }

        public static function share($key, $value = null)
        {
            
            return $instance->share($key, $value);
        }

        public static function incrementRender()
        {
            
            $instance->incrementRender();
        }

        public static function decrementRender()
        {
            
            $instance->decrementRender();
        }

        public static function doneRendering()
        {
            
            return $instance->doneRendering();
        }

        public static function hasRenderedOnce($id)
        {
            
            return $instance->hasRenderedOnce($id);
        }

        public static function markAsRenderedOnce($id)
        {
            
            $instance->markAsRenderedOnce($id);
        }

        public static function addLocation($location)
        {
            
            $instance->addLocation($location);
        }

        public static function prependLocation($location)
        {
            
            $instance->prependLocation($location);
        }

        public static function addNamespace($namespace, $hints)
        {
            
            return $instance->addNamespace($namespace, $hints);
        }

        public static function prependNamespace($namespace, $hints)
        {
            
            return $instance->prependNamespace($namespace, $hints);
        }

        public static function replaceNamespace($namespace, $hints)
        {
            
            return $instance->replaceNamespace($namespace, $hints);
        }

        public static function addExtension($extension, $engine, $resolver = null)
        {
            
            $instance->addExtension($extension, $engine, $resolver);
        }

        public static function flushState()
        {
            
            $instance->flushState();
        }

        public static function flushStateIfDoneRendering()
        {
            
            $instance->flushStateIfDoneRendering();
        }

        public static function getExtensions()
        {
            
            return $instance->getExtensions();
        }

        public static function getEngineResolver()
        {
            
            return $instance->getEngineResolver();
        }

        public static function getFinder()
        {
            
            return $instance->getFinder();
        }

        public static function setFinder($finder)
        {
            
            $instance->setFinder($finder);
        }

        public static function flushFinderCache()
        {
            
            $instance->flushFinderCache();
        }

        public static function getDispatcher()
        {
            
            return $instance->getDispatcher();
        }

        public static function setDispatcher($events)
        {
            
            $instance->setDispatcher($events);
        }

        public static function getContainer()
        {
            
            return $instance->getContainer();
        }

        public static function setContainer($container)
        {
            
            $instance->setContainer($container);
        }

        public static function shared($key, $default = null)
        {
            
            return $instance->shared($key, $default);
        }

        public static function getShared()
        {
            
            return $instance->getShared();
        }

        public static function macro($name, $macro)
        {
            \Illuminate\View\Factory::macro($name, $macro);
        }

        public static function mixin($mixin, $replace = true)
        {
            \Illuminate\View\Factory::mixin($mixin, $replace);
        }

        public static function hasMacro($name)
        {
            return \Illuminate\View\Factory::hasMacro($name);
        }

        public static function flushMacros()
        {
            \Illuminate\View\Factory::flushMacros();
        }

        public static function startComponent($view, $data = [])
        {
            
            $instance->startComponent($view, $data);
        }

        public static function startComponentFirst($names, $data = [])
        {
            
            $instance->startComponentFirst($names, $data);
        }

        public static function renderComponent()
        {
            
            return $instance->renderComponent();
        }

        public static function getConsumableComponentData($key, $default = null)
        {
            
            return $instance->getConsumableComponentData($key, $default);
        }

        public static function slot($name, $content = null, $attributes = [])
        {
            
            $instance->slot($name, $content, $attributes);
        }

        public static function endSlot()
        {
            
            $instance->endSlot();
        }

        public static function creator($views, $callback)
        {
            
            return $instance->creator($views, $callback);
        }

        public static function composers($composers)
        {
            
            return $instance->composers($composers);
        }

        public static function composer($views, $callback)
        {
            
            return $instance->composer($views, $callback);
        }

        public static function callComposer($view)
        {
            
            $instance->callComposer($view);
        }

        public static function callCreator($view)
        {
            
            $instance->callCreator($view);
        }

        public static function startFragment($fragment)
        {
            
            $instance->startFragment($fragment);
        }

        public static function stopFragment()
        {
            
            return $instance->stopFragment();
        }

        public static function getFragment($name, $default = null)
        {
            
            return $instance->getFragment($name, $default);
        }

        public static function getFragments()
        {
            
            return $instance->getFragments();
        }

        public static function flushFragments()
        {
            
            $instance->flushFragments();
        }

        public static function startSection($section, $content = null)
        {
            
            $instance->startSection($section, $content);
        }

        public static function inject($section, $content)
        {
            
            $instance->inject($section, $content);
        }

        public static function yieldSection()
        {
            
            return $instance->yieldSection();
        }

        public static function stopSection($overwrite = false)
        {
            
            return $instance->stopSection($overwrite);
        }

        public static function appendSection()
        {
            
            return $instance->appendSection();
        }

        public static function yieldContent($section, $default = '')
        {
            
            return $instance->yieldContent($section, $default);
        }

        public static function parentPlaceholder($section = '')
        {
            return \Illuminate\View\Factory::parentPlaceholder($section);
        }

        public static function hasSection($name)
        {
            
            return $instance->hasSection($name);
        }

        public static function sectionMissing($name)
        {
            
            return $instance->sectionMissing($name);
        }

        public static function getSection($name, $default = null)
        {
            
            return $instance->getSection($name, $default);
        }

        public static function getSections()
        {
            
            return $instance->getSections();
        }

        public static function flushSections()
        {
            
            $instance->flushSections();
        }

        public static function addLoop($data)
        {
            
            $instance->addLoop($data);
        }

        public static function incrementLoopIndices()
        {
            
            $instance->incrementLoopIndices();
        }

        public static function popLoop()
        {
            
            $instance->popLoop();
        }

        public static function getLastLoop()
        {
            
            return $instance->getLastLoop();
        }

        public static function getLoopStack()
        {
            
            return $instance->getLoopStack();
        }

        public static function startPush($section, $content = '')
        {
            
            $instance->startPush($section, $content);
        }

        public static function stopPush()
        {
            
            return $instance->stopPush();
        }

        public static function startPrepend($section, $content = '')
        {
            
            $instance->startPrepend($section, $content);
        }

        public static function stopPrepend()
        {
            
            return $instance->stopPrepend();
        }

        public static function yieldPushContent($section, $default = '')
        {
            
            return $instance->yieldPushContent($section, $default);
        }

        public static function isStackEmpty($section)
        {
            
            return $instance->isStackEmpty($section);
        }

        public static function flushStacks()
        {
            
            $instance->flushStacks();
        }

        public static function startTranslation($replacements = [])
        {
            
            $instance->startTranslation($replacements);
        }

        public static function renderTranslation()
        {
            
            return $instance->renderTranslation();
        }

            }
    
    class Vite {
        
        public static function preloadedAssets()
        {
            
            return $instance->preloadedAssets();
        }

        public static function cspNonce()
        {
            
            return $instance->cspNonce();
        }

        public static function useCspNonce($nonce = null)
        {
            
            return $instance->useCspNonce($nonce);
        }

        public static function useIntegrityKey($key)
        {
            
            return $instance->useIntegrityKey($key);
        }

        public static function withEntryPoints($entryPoints)
        {
            
            return $instance->withEntryPoints($entryPoints);
        }

        public static function mergeEntryPoints($entryPoints)
        {
            
            return $instance->mergeEntryPoints($entryPoints);
        }

        public static function useManifestFilename($filename)
        {
            
            return $instance->useManifestFilename($filename);
        }

        public static function createAssetPathsUsing($resolver)
        {
            
            return $instance->createAssetPathsUsing($resolver);
        }

        public static function hotFile()
        {
            
            return $instance->hotFile();
        }

        public static function useHotFile($path)
        {
            
            return $instance->useHotFile($path);
        }

        public static function useBuildDirectory($path)
        {
            
            return $instance->useBuildDirectory($path);
        }

        public static function useScriptTagAttributes($attributes)
        {
            
            return $instance->useScriptTagAttributes($attributes);
        }

        public static function useStyleTagAttributes($attributes)
        {
            
            return $instance->useStyleTagAttributes($attributes);
        }

        public static function usePreloadTagAttributes($attributes)
        {
            
            return $instance->usePreloadTagAttributes($attributes);
        }

        public static function prefetch($concurrency = null, $event = 'load')
        {
            
            return $instance->prefetch($concurrency, $event);
        }

        public static function useWaterfallPrefetching($concurrency = null)
        {
            
            return $instance->useWaterfallPrefetching($concurrency);
        }

        public static function useAggressivePrefetching()
        {
            
            return $instance->useAggressivePrefetching();
        }

        public static function usePrefetchStrategy($strategy, $config = [])
        {
            
            return $instance->usePrefetchStrategy($strategy, $config);
        }

        public static function reactRefresh()
        {
            
            return $instance->reactRefresh();
        }

        public static function asset($asset, $buildDirectory = null)
        {
            
            return $instance->asset($asset, $buildDirectory);
        }

        public static function content($asset, $buildDirectory = null)
        {
            
            return $instance->content($asset, $buildDirectory);
        }

        public static function manifestHash($buildDirectory = null)
        {
            
            return $instance->manifestHash($buildDirectory);
        }

        public static function isRunningHot()
        {
            
            return $instance->isRunningHot();
        }

        public static function toHtml()
        {
            
            return $instance->toHtml();
        }

        public static function flush()
        {
            
            $instance->flush();
        }

        public static function macro($name, $macro)
        {
            \Illuminate\Foundation\Vite::macro($name, $macro);
        }

        public static function mixin($mixin, $replace = true)
        {
            \Illuminate\Foundation\Vite::mixin($mixin, $replace);
        }

        public static function hasMacro($name)
        {
            return \Illuminate\Foundation\Vite::hasMacro($name);
        }

        public static function flushMacros()
        {
            \Illuminate\Foundation\Vite::flushMacros();
        }

            }
    }

namespace Illuminate\Http {
    
    class Request extends \Symfony\Component\HttpFoundation\Request {
        
        public static function validate($rules, ...$params)
        {
            return \Illuminate\Http\Request::validate($rules, ...$params);
        }

        public static function validateWithBag($errorBag, $rules, ...$params)
        {
            return \Illuminate\Http\Request::validateWithBag($errorBag, $rules, ...$params);
        }

        public static function hasValidSignature($absolute = true)
        {
            return \Illuminate\Http\Request::hasValidSignature($absolute);
        }

        public static function hasValidRelativeSignature()
        {
            return \Illuminate\Http\Request::hasValidRelativeSignature();
        }

        public static function hasValidSignatureWhileIgnoring($ignoreQuery = [], $absolute = true)
        {
            return \Illuminate\Http\Request::hasValidSignatureWhileIgnoring($ignoreQuery, $absolute);
        }

        public static function hasValidRelativeSignatureWhileIgnoring($ignoreQuery = [])
        {
            return \Illuminate\Http\Request::hasValidRelativeSignatureWhileIgnoring($ignoreQuery);
        }

            }
    }

namespace  {
    class App extends \Illuminate\Support\Facades\App {}
    class Arr extends \Illuminate\Support\Arr {}
    class Artisan extends \Illuminate\Support\Facades\Artisan {}
    class Auth extends \Illuminate\Support\Facades\Auth {}
    class Benchmark extends \Illuminate\Support\Benchmark {}
    class Blade extends \Illuminate\Support\Facades\Blade {}
    class Broadcast extends \Illuminate\Support\Facades\Broadcast {}
    class Bus extends \Illuminate\Support\Facades\Bus {}
    class Cache extends \Illuminate\Support\Facades\Cache {}
    class Concurrency extends \Illuminate\Support\Facades\Concurrency {}
    class Config extends \Illuminate\Support\Facades\Config {}
    class Context extends \Illuminate\Support\Facades\Context {}
    class Cookie extends \Illuminate\Support\Facades\Cookie {}
    class Crypt extends \Illuminate\Support\Facades\Crypt {}
    class Date extends \Illuminate\Support\Facades\Date {}
    class DB extends \Illuminate\Support\Facades\DB {}

    class Eloquent extends \Illuminate\Database\Eloquent\Model {        
        public static function make($attributes = [])
        {
            
            return $instance->make($attributes);
        }

        public static function withGlobalScope($identifier, $scope)
        {
            
            return $instance->withGlobalScope($identifier, $scope);
        }

        public static function withoutGlobalScope($scope)
        {
            
            return $instance->withoutGlobalScope($scope);
        }

        public static function withoutGlobalScopes($scopes = null)
        {
            
            return $instance->withoutGlobalScopes($scopes);
        }

        public static function withoutGlobalScopesExcept($scopes = [])
        {
            
            return $instance->withoutGlobalScopesExcept($scopes);
        }

        public static function removedScopes()
        {
            
            return $instance->removedScopes();
        }

        public static function whereKey($id)
        {
            
            return $instance->whereKey($id);
        }

        public static function whereKeyNot($id)
        {
            
            return $instance->whereKeyNot($id);
        }

        public static function where($column, $operator = null, $value = null, $boolean = 'and')
        {
            
            return $instance->where($column, $operator, $value, $boolean);
        }

        public static function firstWhere($column, $operator = null, $value = null, $boolean = 'and')
        {
            
            return $instance->firstWhere($column, $operator, $value, $boolean);
        }

        public static function orWhere($column, $operator = null, $value = null)
        {
            
            return $instance->orWhere($column, $operator, $value);
        }

        public static function whereNot($column, $operator = null, $value = null, $boolean = 'and')
        {
            
            return $instance->whereNot($column, $operator, $value, $boolean);
        }

        public static function orWhereNot($column, $operator = null, $value = null)
        {
            
            return $instance->orWhereNot($column, $operator, $value);
        }

        public static function latest($column = null)
        {
            
            return $instance->latest($column);
        }

        public static function oldest($column = null)
        {
            
            return $instance->oldest($column);
        }

        public static function hydrate($items)
        {
            
            return $instance->hydrate($items);
        }

        public static function fillAndInsert($values)
        {
            
            return $instance->fillAndInsert($values);
        }

        public static function fillAndInsertOrIgnore($values)
        {
            
            return $instance->fillAndInsertOrIgnore($values);
        }

        public static function fillAndInsertGetId($values)
        {
            
            return $instance->fillAndInsertGetId($values);
        }

        public static function fillForInsert($values)
        {
            
            return $instance->fillForInsert($values);
        }

        public static function fromQuery($query, $bindings = [])
        {
            
            return $instance->fromQuery($query, $bindings);
        }

        public static function find($id, $columns = [])
        {
            
            return $instance->find($id, $columns);
        }

        public static function findSole($id, $columns = [])
        {
            
            return $instance->findSole($id, $columns);
        }

        public static function findMany($ids, $columns = [])
        {
            
            return $instance->findMany($ids, $columns);
        }

        public static function findOrFail($id, $columns = [])
        {
            
            return $instance->findOrFail($id, $columns);
        }

        public static function findOrNew($id, $columns = [])
        {
            
            return $instance->findOrNew($id, $columns);
        }

        public static function findOr($id, $columns = [], $callback = null)
        {
            
            return $instance->findOr($id, $columns, $callback);
        }

        public static function firstOrNew($attributes = [], $values = [])
        {
            
            return $instance->firstOrNew($attributes, $values);
        }

        public static function firstOrCreate($attributes = [], $values = [])
        {
            
            return $instance->firstOrCreate($attributes, $values);
        }

        public static function createOrFirst($attributes = [], $values = [])
        {
            
            return $instance->createOrFirst($attributes, $values);
        }

        public static function updateOrCreate($attributes, $values = [])
        {
            
            return $instance->updateOrCreate($attributes, $values);
        }

        public static function incrementOrCreate($attributes, $column = 'count', $default = 1, $step = 1, $extra = [])
        {
            
            return $instance->incrementOrCreate($attributes, $column, $default, $step, $extra);
        }

        public static function firstOrFail($columns = [])
        {
            
            return $instance->firstOrFail($columns);
        }

        public static function firstOr($columns = [], $callback = null)
        {
            
            return $instance->firstOr($columns, $callback);
        }

        public static function sole($columns = [])
        {
            
            return $instance->sole($columns);
        }

        public static function value($column)
        {
            
            return $instance->value($column);
        }

        public static function soleValue($column)
        {
            
            return $instance->soleValue($column);
        }

        public static function valueOrFail($column)
        {
            
            return $instance->valueOrFail($column);
        }

        public static function get($columns = [])
        {
            
            return $instance->get($columns);
        }

        public static function getModels($columns = [])
        {
            
            return $instance->getModels($columns);
        }

        public static function eagerLoadRelations($models)
        {
            
            return $instance->eagerLoadRelations($models);
        }

        public static function afterQuery($callback)
        {
            
            return $instance->afterQuery($callback);
        }

        public static function applyAfterQueryCallbacks($result)
        {
            
            return $instance->applyAfterQueryCallbacks($result);
        }

        public static function cursor()
        {
            
            return $instance->cursor();
        }

        public static function pluck($column, $key = null)
        {
            
            return $instance->pluck($column, $key);
        }

        public static function paginate($perPage = null, $columns = [], $pageName = 'page', $page = null, $total = null)
        {
            
            return $instance->paginate($perPage, $columns, $pageName, $page, $total);
        }

        public static function simplePaginate($perPage = null, $columns = [], $pageName = 'page', $page = null)
        {
            
            return $instance->simplePaginate($perPage, $columns, $pageName, $page);
        }

        public static function cursorPaginate($perPage = null, $columns = [], $cursorName = 'cursor', $cursor = null)
        {
            
            return $instance->cursorPaginate($perPage, $columns, $cursorName, $cursor);
        }

        public static function create($attributes = [])
        {
            
            return $instance->create($attributes);
        }

        public static function createQuietly($attributes = [])
        {
            
            return $instance->createQuietly($attributes);
        }

        public static function forceCreate($attributes)
        {
            
            return $instance->forceCreate($attributes);
        }

        public static function forceCreateQuietly($attributes = [])
        {
            
            return $instance->forceCreateQuietly($attributes);
        }

        public static function upsert($values, $uniqueBy, $update = null)
        {
            
            return $instance->upsert($values, $uniqueBy, $update);
        }

        public static function onDelete($callback)
        {
            
            $instance->onDelete($callback);
        }

        public static function scopes($scopes)
        {
            
            return $instance->scopes($scopes);
        }

        public static function applyScopes()
        {
            
            return $instance->applyScopes();
        }

        public static function without($relations)
        {
            
            return $instance->without($relations);
        }

        public static function withOnly($relations)
        {
            
            return $instance->withOnly($relations);
        }

        public static function newModelInstance($attributes = [])
        {
            
            return $instance->newModelInstance($attributes);
        }

        public static function withAttributes($attributes, $value = null, $asConditions = true)
        {
            
            return $instance->withAttributes($attributes, $value, $asConditions);
        }

        public static function withCasts($casts)
        {
            
            return $instance->withCasts($casts);
        }

        public static function withSavepointIfNeeded($scope)
        {
            
            return $instance->withSavepointIfNeeded($scope);
        }

        public static function getQuery()
        {
            
            return $instance->getQuery();
        }

        public static function setQuery($query)
        {
            
            return $instance->setQuery($query);
        }

        public static function toBase()
        {
            
            return $instance->toBase();
        }

        public static function getEagerLoads()
        {
            
            return $instance->getEagerLoads();
        }

        public static function setEagerLoads($eagerLoad)
        {
            
            return $instance->setEagerLoads($eagerLoad);
        }

        public static function withoutEagerLoad($relations)
        {
            
            return $instance->withoutEagerLoad($relations);
        }

        public static function withoutEagerLoads()
        {
            
            return $instance->withoutEagerLoads();
        }

        public static function getLimit()
        {
            
            return $instance->getLimit();
        }

        public static function getOffset()
        {
            
            return $instance->getOffset();
        }

        public static function getModel()
        {
            
            return $instance->getModel();
        }

        public static function setModel($model)
        {
            
            return $instance->setModel($model);
        }

        public static function getMacro($name)
        {
            
            return $instance->getMacro($name);
        }

        public static function hasMacro($name)
        {
            
            return $instance->hasMacro($name);
        }

        public static function getGlobalMacro($name)
        {
            return \Illuminate\Database\Eloquent\Builder::getGlobalMacro($name);
        }

        public static function hasGlobalMacro($name)
        {
            return \Illuminate\Database\Eloquent\Builder::hasGlobalMacro($name);
        }

        public static function clone()
        {
            
            return $instance->clone();
        }

        public static function onClone($callback)
        {
            
            return $instance->onClone($callback);
        }

        public static function chunk($count, $callback)
        {
            
            return $instance->chunk($count, $callback);
        }

        public static function chunkMap($callback, $count = 1000)
        {
            
            return $instance->chunkMap($callback, $count);
        }

        public static function each($callback, $count = 1000)
        {
            
            return $instance->each($callback, $count);
        }

        public static function chunkById($count, $callback, $column = null, $alias = null)
        {
            
            return $instance->chunkById($count, $callback, $column, $alias);
        }

        public static function chunkByIdDesc($count, $callback, $column = null, $alias = null)
        {
            
            return $instance->chunkByIdDesc($count, $callback, $column, $alias);
        }

        public static function orderedChunkById($count, $callback, $column = null, $alias = null, $descending = false)
        {
            
            return $instance->orderedChunkById($count, $callback, $column, $alias, $descending);
        }

        public static function eachById($callback, $count = 1000, $column = null, $alias = null)
        {
            
            return $instance->eachById($callback, $count, $column, $alias);
        }

        public static function lazy($chunkSize = 1000)
        {
            
            return $instance->lazy($chunkSize);
        }

        public static function lazyById($chunkSize = 1000, $column = null, $alias = null)
        {
            
            return $instance->lazyById($chunkSize, $column, $alias);
        }

        public static function lazyByIdDesc($chunkSize = 1000, $column = null, $alias = null)
        {
            
            return $instance->lazyByIdDesc($chunkSize, $column, $alias);
        }

        public static function first($columns = [])
        {
            
            return $instance->first($columns);
        }

        public static function baseSole($columns = [])
        {
            
            return $instance->baseSole($columns);
        }

        public static function tap($callback)
        {
            
            return $instance->tap($callback);
        }

        public static function pipe($callback)
        {
            
            return $instance->pipe($callback);
        }

        public static function when($value = null, $callback = null, $default = null)
        {
            
            return $instance->when($value, $callback, $default);
        }

        public static function unless($value = null, $callback = null, $default = null)
        {
            
            return $instance->unless($value, $callback, $default);
        }

        public static function has($relation, $operator = '>=', $count = 1, $boolean = 'and', $callback = null)
        {
            
            return $instance->has($relation, $operator, $count, $boolean, $callback);
        }

        public static function orHas($relation, $operator = '>=', $count = 1)
        {
            
            return $instance->orHas($relation, $operator, $count);
        }

        public static function doesntHave($relation, $boolean = 'and', $callback = null)
        {
            
            return $instance->doesntHave($relation, $boolean, $callback);
        }

        public static function orDoesntHave($relation)
        {
            
            return $instance->orDoesntHave($relation);
        }

        public static function whereHas($relation, $callback = null, $operator = '>=', $count = 1)
        {
            
            return $instance->whereHas($relation, $callback, $operator, $count);
        }

        public static function withWhereHas($relation, $callback = null, $operator = '>=', $count = 1)
        {
            
            return $instance->withWhereHas($relation, $callback, $operator, $count);
        }

        public static function orWhereHas($relation, $callback = null, $operator = '>=', $count = 1)
        {
            
            return $instance->orWhereHas($relation, $callback, $operator, $count);
        }

        public static function whereDoesntHave($relation, $callback = null)
        {
            
            return $instance->whereDoesntHave($relation, $callback);
        }

        public static function orWhereDoesntHave($relation, $callback = null)
        {
            
            return $instance->orWhereDoesntHave($relation, $callback);
        }

        public static function hasMorph($relation, $types, $operator = '>=', $count = 1, $boolean = 'and', $callback = null)
        {
            
            return $instance->hasMorph($relation, $types, $operator, $count, $boolean, $callback);
        }

        public static function orHasMorph($relation, $types, $operator = '>=', $count = 1)
        {
            
            return $instance->orHasMorph($relation, $types, $operator, $count);
        }

        public static function doesntHaveMorph($relation, $types, $boolean = 'and', $callback = null)
        {
            
            return $instance->doesntHaveMorph($relation, $types, $boolean, $callback);
        }

        public static function orDoesntHaveMorph($relation, $types)
        {
            
            return $instance->orDoesntHaveMorph($relation, $types);
        }

        public static function whereHasMorph($relation, $types, $callback = null, $operator = '>=', $count = 1)
        {
            
            return $instance->whereHasMorph($relation, $types, $callback, $operator, $count);
        }

        public static function orWhereHasMorph($relation, $types, $callback = null, $operator = '>=', $count = 1)
        {
            
            return $instance->orWhereHasMorph($relation, $types, $callback, $operator, $count);
        }

        public static function whereDoesntHaveMorph($relation, $types, $callback = null)
        {
            
            return $instance->whereDoesntHaveMorph($relation, $types, $callback);
        }

        public static function orWhereDoesntHaveMorph($relation, $types, $callback = null)
        {
            
            return $instance->orWhereDoesntHaveMorph($relation, $types, $callback);
        }

        public static function whereRelation($relation, $column, $operator = null, $value = null)
        {
            
            return $instance->whereRelation($relation, $column, $operator, $value);
        }

        public static function withWhereRelation($relation, $column, $operator = null, $value = null)
        {
            
            return $instance->withWhereRelation($relation, $column, $operator, $value);
        }

        public static function orWhereRelation($relation, $column, $operator = null, $value = null)
        {
            
            return $instance->orWhereRelation($relation, $column, $operator, $value);
        }

        public static function whereDoesntHaveRelation($relation, $column, $operator = null, $value = null)
        {
            
            return $instance->whereDoesntHaveRelation($relation, $column, $operator, $value);
        }

        public static function orWhereDoesntHaveRelation($relation, $column, $operator = null, $value = null)
        {
            
            return $instance->orWhereDoesntHaveRelation($relation, $column, $operator, $value);
        }

        public static function whereMorphRelation($relation, $types, $column, $operator = null, $value = null)
        {
            
            return $instance->whereMorphRelation($relation, $types, $column, $operator, $value);
        }

        public static function orWhereMorphRelation($relation, $types, $column, $operator = null, $value = null)
        {
            
            return $instance->orWhereMorphRelation($relation, $types, $column, $operator, $value);
        }

        public static function whereMorphDoesntHaveRelation($relation, $types, $column, $operator = null, $value = null)
        {
            
            return $instance->whereMorphDoesntHaveRelation($relation, $types, $column, $operator, $value);
        }

        public static function orWhereMorphDoesntHaveRelation($relation, $types, $column, $operator = null, $value = null)
        {
            
            return $instance->orWhereMorphDoesntHaveRelation($relation, $types, $column, $operator, $value);
        }

        public static function whereMorphedTo($relation, $model, $boolean = 'and')
        {
            
            return $instance->whereMorphedTo($relation, $model, $boolean);
        }

        public static function whereNotMorphedTo($relation, $model, $boolean = 'and')
        {
            
            return $instance->whereNotMorphedTo($relation, $model, $boolean);
        }

        public static function orWhereMorphedTo($relation, $model)
        {
            
            return $instance->orWhereMorphedTo($relation, $model);
        }

        public static function orWhereNotMorphedTo($relation, $model)
        {
            
            return $instance->orWhereNotMorphedTo($relation, $model);
        }

        public static function whereBelongsTo($related, $relationshipName = null, $boolean = 'and')
        {
            
            return $instance->whereBelongsTo($related, $relationshipName, $boolean);
        }

        public static function orWhereBelongsTo($related, $relationshipName = null)
        {
            
            return $instance->orWhereBelongsTo($related, $relationshipName);
        }

        public static function whereAttachedTo($related, $relationshipName = null, $boolean = 'and')
        {
            
            return $instance->whereAttachedTo($related, $relationshipName, $boolean);
        }

        public static function orWhereAttachedTo($related, $relationshipName = null)
        {
            
            return $instance->orWhereAttachedTo($related, $relationshipName);
        }

        public static function withAggregate($relations, $column, $function = null)
        {
            
            return $instance->withAggregate($relations, $column, $function);
        }

        public static function withCount($relations)
        {
            
            return $instance->withCount($relations);
        }

        public static function withMax($relation, $column)
        {
            
            return $instance->withMax($relation, $column);
        }

        public static function withMin($relation, $column)
        {
            
            return $instance->withMin($relation, $column);
        }

        public static function withSum($relation, $column)
        {
            
            return $instance->withSum($relation, $column);
        }

        public static function withAvg($relation, $column)
        {
            
            return $instance->withAvg($relation, $column);
        }

        public static function withExists($relation)
        {
            
            return $instance->withExists($relation);
        }

        public static function mergeConstraintsFrom($from)
        {
            
            return $instance->mergeConstraintsFrom($from);
        }

        public static function select($columns = [])
        {
            
            return $instance->select($columns);
        }

        public static function selectSub($query, $as)
        {
            
            return $instance->selectSub($query, $as);
        }

        public static function selectExpression($expression, $as)
        {
            
            return $instance->selectExpression($expression, $as);
        }

        public static function selectRaw($expression, $bindings = [])
        {
            
            return $instance->selectRaw($expression, $bindings);
        }

        public static function fromSub($query, $as)
        {
            
            return $instance->fromSub($query, $as);
        }

        public static function fromRaw($expression, $bindings = [])
        {
            
            return $instance->fromRaw($expression, $bindings);
        }

        public static function addSelect($column)
        {
            
            return $instance->addSelect($column);
        }

        public static function selectVectorDistance($column, $vector, $as = null)
        {
            
            return $instance->selectVectorDistance($column, $vector, $as);
        }

        public static function distinct()
        {
            
            return $instance->distinct();
        }

        public static function from($table, $as = null)
        {
            
            return $instance->from($table, $as);
        }

        public static function useIndex($index)
        {
            
            return $instance->useIndex($index);
        }

        public static function forceIndex($index)
        {
            
            return $instance->forceIndex($index);
        }

        public static function ignoreIndex($index)
        {
            
            return $instance->ignoreIndex($index);
        }

        public static function join($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
        {
            
            return $instance->join($table, $first, $operator, $second, $type, $where);
        }

        public static function joinWhere($table, $first, $operator, $second, $type = 'inner')
        {
            
            return $instance->joinWhere($table, $first, $operator, $second, $type);
        }

        public static function joinSub($query, $as, $first, $operator = null, $second = null, $type = 'inner', $where = false)
        {
            
            return $instance->joinSub($query, $as, $first, $operator, $second, $type, $where);
        }

        public static function joinLateral($query, $as, $type = 'inner')
        {
            
            return $instance->joinLateral($query, $as, $type);
        }

        public static function leftJoinLateral($query, $as)
        {
            
            return $instance->leftJoinLateral($query, $as);
        }

        public static function leftJoin($table, $first, $operator = null, $second = null)
        {
            
            return $instance->leftJoin($table, $first, $operator, $second);
        }

        public static function leftJoinWhere($table, $first, $operator, $second)
        {
            
            return $instance->leftJoinWhere($table, $first, $operator, $second);
        }

        public static function leftJoinSub($query, $as, $first, $operator = null, $second = null)
        {
            
            return $instance->leftJoinSub($query, $as, $first, $operator, $second);
        }

        public static function rightJoin($table, $first, $operator = null, $second = null)
        {
            
            return $instance->rightJoin($table, $first, $operator, $second);
        }

        public static function rightJoinWhere($table, $first, $operator, $second)
        {
            
            return $instance->rightJoinWhere($table, $first, $operator, $second);
        }

        public static function rightJoinSub($query, $as, $first, $operator = null, $second = null)
        {
            
            return $instance->rightJoinSub($query, $as, $first, $operator, $second);
        }

        public static function crossJoin($table, $first = null, $operator = null, $second = null)
        {
            
            return $instance->crossJoin($table, $first, $operator, $second);
        }

        public static function crossJoinSub($query, $as)
        {
            
            return $instance->crossJoinSub($query, $as);
        }

        public static function mergeWheres($wheres, $bindings)
        {
            
            return $instance->mergeWheres($wheres, $bindings);
        }

        public static function prepareValueAndOperator($value, $operator, $useDefault = false)
        {
            
            return $instance->prepareValueAndOperator($value, $operator, $useDefault);
        }

        public static function whereColumn($first, $operator = null, $second = null, $boolean = 'and')
        {
            
            return $instance->whereColumn($first, $operator, $second, $boolean);
        }

        public static function orWhereColumn($first, $operator = null, $second = null)
        {
            
            return $instance->orWhereColumn($first, $operator, $second);
        }

        public static function whereVectorSimilarTo($column, $vector, $minSimilarity = 0.6, $order = true)
        {
            
            return $instance->whereVectorSimilarTo($column, $vector, $minSimilarity, $order);
        }

        public static function whereVectorDistanceLessThan($column, $vector, $maxDistance, $boolean = 'and')
        {
            
            return $instance->whereVectorDistanceLessThan($column, $vector, $maxDistance, $boolean);
        }

        public static function orWhereVectorDistanceLessThan($column, $vector, $maxDistance)
        {
            
            return $instance->orWhereVectorDistanceLessThan($column, $vector, $maxDistance);
        }

        public static function whereRaw($sql, $bindings = [], $boolean = 'and')
        {
            
            return $instance->whereRaw($sql, $bindings, $boolean);
        }

        public static function orWhereRaw($sql, $bindings = [])
        {
            
            return $instance->orWhereRaw($sql, $bindings);
        }

        public static function whereLike($column, $value, $caseSensitive = false, $boolean = 'and', $not = false)
        {
            
            return $instance->whereLike($column, $value, $caseSensitive, $boolean, $not);
        }

        public static function orWhereLike($column, $value, $caseSensitive = false)
        {
            
            return $instance->orWhereLike($column, $value, $caseSensitive);
        }

        public static function whereNotLike($column, $value, $caseSensitive = false, $boolean = 'and')
        {
            
            return $instance->whereNotLike($column, $value, $caseSensitive, $boolean);
        }

        public static function orWhereNotLike($column, $value, $caseSensitive = false)
        {
            
            return $instance->orWhereNotLike($column, $value, $caseSensitive);
        }

        public static function whereIn($column, $values, $boolean = 'and', $not = false)
        {
            
            return $instance->whereIn($column, $values, $boolean, $not);
        }

        public static function orWhereIn($column, $values)
        {
            
            return $instance->orWhereIn($column, $values);
        }

        public static function whereNotIn($column, $values, $boolean = 'and')
        {
            
            return $instance->whereNotIn($column, $values, $boolean);
        }

        public static function orWhereNotIn($column, $values)
        {
            
            return $instance->orWhereNotIn($column, $values);
        }

        public static function whereIntegerInRaw($column, $values, $boolean = 'and', $not = false)
        {
            
            return $instance->whereIntegerInRaw($column, $values, $boolean, $not);
        }

        public static function orWhereIntegerInRaw($column, $values)
        {
            
            return $instance->orWhereIntegerInRaw($column, $values);
        }

        public static function whereIntegerNotInRaw($column, $values, $boolean = 'and')
        {
            
            return $instance->whereIntegerNotInRaw($column, $values, $boolean);
        }

        public static function orWhereIntegerNotInRaw($column, $values)
        {
            
            return $instance->orWhereIntegerNotInRaw($column, $values);
        }

        public static function whereNull($columns, $boolean = 'and', $not = false)
        {
            
            return $instance->whereNull($columns, $boolean, $not);
        }

        public static function orWhereNull($column)
        {
            
            return $instance->orWhereNull($column);
        }

        public static function whereNotNull($columns, $boolean = 'and')
        {
            
            return $instance->whereNotNull($columns, $boolean);
        }

        public static function whereBetween($column, $values, $boolean = 'and', $not = false)
        {
            
            return $instance->whereBetween($column, $values, $boolean, $not);
        }

        public static function whereBetweenColumns($column, $values, $boolean = 'and', $not = false)
        {
            
            return $instance->whereBetweenColumns($column, $values, $boolean, $not);
        }

        public static function orWhereBetween($column, $values)
        {
            
            return $instance->orWhereBetween($column, $values);
        }

        public static function orWhereBetweenColumns($column, $values)
        {
            
            return $instance->orWhereBetweenColumns($column, $values);
        }

        public static function whereNotBetween($column, $values, $boolean = 'and')
        {
            
            return $instance->whereNotBetween($column, $values, $boolean);
        }

        public static function whereNotBetweenColumns($column, $values, $boolean = 'and')
        {
            
            return $instance->whereNotBetweenColumns($column, $values, $boolean);
        }

        public static function orWhereNotBetween($column, $values)
        {
            
            return $instance->orWhereNotBetween($column, $values);
        }

        public static function orWhereNotBetweenColumns($column, $values)
        {
            
            return $instance->orWhereNotBetweenColumns($column, $values);
        }

        public static function whereValueBetween($value, $columns, $boolean = 'and', $not = false)
        {
            
            return $instance->whereValueBetween($value, $columns, $boolean, $not);
        }

        public static function orWhereValueBetween($value, $columns)
        {
            
            return $instance->orWhereValueBetween($value, $columns);
        }

        public static function whereValueNotBetween($value, $columns, $boolean = 'and')
        {
            
            return $instance->whereValueNotBetween($value, $columns, $boolean);
        }

        public static function orWhereValueNotBetween($value, $columns)
        {
            
            return $instance->orWhereValueNotBetween($value, $columns);
        }

        public static function orWhereNotNull($column)
        {
            
            return $instance->orWhereNotNull($column);
        }

        public static function whereDate($column, $operator, $value = null, $boolean = 'and')
        {
            
            return $instance->whereDate($column, $operator, $value, $boolean);
        }

        public static function orWhereDate($column, $operator, $value = null)
        {
            
            return $instance->orWhereDate($column, $operator, $value);
        }

        public static function whereTime($column, $operator, $value = null, $boolean = 'and')
        {
            
            return $instance->whereTime($column, $operator, $value, $boolean);
        }

        public static function orWhereTime($column, $operator, $value = null)
        {
            
            return $instance->orWhereTime($column, $operator, $value);
        }

        public static function whereDay($column, $operator, $value = null, $boolean = 'and')
        {
            
            return $instance->whereDay($column, $operator, $value, $boolean);
        }

        public static function orWhereDay($column, $operator, $value = null)
        {
            
            return $instance->orWhereDay($column, $operator, $value);
        }

        public static function whereMonth($column, $operator, $value = null, $boolean = 'and')
        {
            
            return $instance->whereMonth($column, $operator, $value, $boolean);
        }

        public static function orWhereMonth($column, $operator, $value = null)
        {
            
            return $instance->orWhereMonth($column, $operator, $value);
        }

        public static function whereYear($column, $operator, $value = null, $boolean = 'and')
        {
            
            return $instance->whereYear($column, $operator, $value, $boolean);
        }

        public static function orWhereYear($column, $operator, $value = null)
        {
            
            return $instance->orWhereYear($column, $operator, $value);
        }

        public static function whereNested($callback, $boolean = 'and')
        {
            
            return $instance->whereNested($callback, $boolean);
        }

        public static function forNestedWhere()
        {
            
            return $instance->forNestedWhere();
        }

        public static function addNestedWhereQuery($query, $boolean = 'and')
        {
            
            return $instance->addNestedWhereQuery($query, $boolean);
        }

        public static function whereExists($callback, $boolean = 'and', $not = false)
        {
            
            return $instance->whereExists($callback, $boolean, $not);
        }

        public static function orWhereExists($callback, $not = false)
        {
            
            return $instance->orWhereExists($callback, $not);
        }

        public static function whereNotExists($callback, $boolean = 'and')
        {
            
            return $instance->whereNotExists($callback, $boolean);
        }

        public static function orWhereNotExists($callback)
        {
            
            return $instance->orWhereNotExists($callback);
        }

        public static function addWhereExistsQuery($query, $boolean = 'and', $not = false)
        {
            
            return $instance->addWhereExistsQuery($query, $boolean, $not);
        }

        public static function whereRowValues($columns, $operator, $values, $boolean = 'and')
        {
            
            return $instance->whereRowValues($columns, $operator, $values, $boolean);
        }

        public static function orWhereRowValues($columns, $operator, $values)
        {
            
            return $instance->orWhereRowValues($columns, $operator, $values);
        }

        public static function whereJsonContains($column, $value, $boolean = 'and', $not = false)
        {
            
            return $instance->whereJsonContains($column, $value, $boolean, $not);
        }

        public static function orWhereJsonContains($column, $value)
        {
            
            return $instance->orWhereJsonContains($column, $value);
        }

        public static function whereJsonDoesntContain($column, $value, $boolean = 'and')
        {
            
            return $instance->whereJsonDoesntContain($column, $value, $boolean);
        }

        public static function orWhereJsonDoesntContain($column, $value)
        {
            
            return $instance->orWhereJsonDoesntContain($column, $value);
        }

        public static function whereJsonOverlaps($column, $value, $boolean = 'and', $not = false)
        {
            
            return $instance->whereJsonOverlaps($column, $value, $boolean, $not);
        }

        public static function orWhereJsonOverlaps($column, $value)
        {
            
            return $instance->orWhereJsonOverlaps($column, $value);
        }

        public static function whereJsonDoesntOverlap($column, $value, $boolean = 'and')
        {
            
            return $instance->whereJsonDoesntOverlap($column, $value, $boolean);
        }

        public static function orWhereJsonDoesntOverlap($column, $value)
        {
            
            return $instance->orWhereJsonDoesntOverlap($column, $value);
        }

        public static function whereJsonContainsKey($column, $boolean = 'and', $not = false)
        {
            
            return $instance->whereJsonContainsKey($column, $boolean, $not);
        }

        public static function orWhereJsonContainsKey($column)
        {
            
            return $instance->orWhereJsonContainsKey($column);
        }

        public static function whereJsonDoesntContainKey($column, $boolean = 'and')
        {
            
            return $instance->whereJsonDoesntContainKey($column, $boolean);
        }

        public static function orWhereJsonDoesntContainKey($column)
        {
            
            return $instance->orWhereJsonDoesntContainKey($column);
        }

        public static function whereJsonLength($column, $operator, $value = null, $boolean = 'and')
        {
            
            return $instance->whereJsonLength($column, $operator, $value, $boolean);
        }

        public static function orWhereJsonLength($column, $operator, $value = null)
        {
            
            return $instance->orWhereJsonLength($column, $operator, $value);
        }

        public static function dynamicWhere($method, $parameters)
        {
            
            return $instance->dynamicWhere($method, $parameters);
        }

        public static function whereFullText($columns, $value, $options = [], $boolean = 'and')
        {
            
            return $instance->whereFullText($columns, $value, $options, $boolean);
        }

        public static function orWhereFullText($columns, $value, $options = [])
        {
            
            return $instance->orWhereFullText($columns, $value, $options);
        }

        public static function whereAll($columns, $operator = null, $value = null, $boolean = 'and')
        {
            
            return $instance->whereAll($columns, $operator, $value, $boolean);
        }

        public static function orWhereAll($columns, $operator = null, $value = null)
        {
            
            return $instance->orWhereAll($columns, $operator, $value);
        }

        public static function whereAny($columns, $operator = null, $value = null, $boolean = 'and')
        {
            
            return $instance->whereAny($columns, $operator, $value, $boolean);
        }

        public static function orWhereAny($columns, $operator = null, $value = null)
        {
            
            return $instance->orWhereAny($columns, $operator, $value);
        }

        public static function whereNone($columns, $operator = null, $value = null, $boolean = 'and')
        {
            
            return $instance->whereNone($columns, $operator, $value, $boolean);
        }

        public static function orWhereNone($columns, $operator = null, $value = null)
        {
            
            return $instance->orWhereNone($columns, $operator, $value);
        }

        public static function groupBy(...$groups)
        {
            
            return $instance->groupBy(...$groups);
        }

        public static function groupByRaw($sql, $bindings = [])
        {
            
            return $instance->groupByRaw($sql, $bindings);
        }

        public static function having($column, $operator = null, $value = null, $boolean = 'and')
        {
            
            return $instance->having($column, $operator, $value, $boolean);
        }

        public static function orHaving($column, $operator = null, $value = null)
        {
            
            return $instance->orHaving($column, $operator, $value);
        }

        public static function havingNested($callback, $boolean = 'and')
        {
            
            return $instance->havingNested($callback, $boolean);
        }

        public static function addNestedHavingQuery($query, $boolean = 'and')
        {
            
            return $instance->addNestedHavingQuery($query, $boolean);
        }

        public static function havingNull($columns, $boolean = 'and', $not = false)
        {
            
            return $instance->havingNull($columns, $boolean, $not);
        }

        public static function orHavingNull($column)
        {
            
            return $instance->orHavingNull($column);
        }

        public static function havingNotNull($columns, $boolean = 'and')
        {
            
            return $instance->havingNotNull($columns, $boolean);
        }

        public static function orHavingNotNull($column)
        {
            
            return $instance->orHavingNotNull($column);
        }

        public static function havingBetween($column, $values, $boolean = 'and', $not = false)
        {
            
            return $instance->havingBetween($column, $values, $boolean, $not);
        }

        public static function havingNotBetween($column, $values, $boolean = 'and')
        {
            
            return $instance->havingNotBetween($column, $values, $boolean);
        }

        public static function orHavingBetween($column, $values)
        {
            
            return $instance->orHavingBetween($column, $values);
        }

        public static function orHavingNotBetween($column, $values)
        {
            
            return $instance->orHavingNotBetween($column, $values);
        }

        public static function havingRaw($sql, $bindings = [], $boolean = 'and')
        {
            
            return $instance->havingRaw($sql, $bindings, $boolean);
        }

        public static function orHavingRaw($sql, $bindings = [])
        {
            
            return $instance->orHavingRaw($sql, $bindings);
        }

        public static function orderBy($column, $direction = 'asc')
        {
            
            return $instance->orderBy($column, $direction);
        }

        public static function orderByDesc($column)
        {
            
            return $instance->orderByDesc($column);
        }

        public static function orderByVectorDistance($column, $vector)
        {
            
            return $instance->orderByVectorDistance($column, $vector);
        }

        public static function inRandomOrder($seed = '')
        {
            
            return $instance->inRandomOrder($seed);
        }

        public static function orderByRaw($sql, $bindings = [])
        {
            
            return $instance->orderByRaw($sql, $bindings);
        }

        public static function skip($value)
        {
            
            return $instance->skip($value);
        }

        public static function offset($value)
        {
            
            return $instance->offset($value);
        }

        public static function take($value)
        {
            
            return $instance->take($value);
        }

        public static function limit($value)
        {
            
            return $instance->limit($value);
        }

        public static function groupLimit($value, $column)
        {
            
            return $instance->groupLimit($value, $column);
        }

        public static function forPage($page, $perPage = 15)
        {
            
            return $instance->forPage($page, $perPage);
        }

        public static function forPageBeforeId($perPage = 15, $lastId = 0, $column = 'id')
        {
            
            return $instance->forPageBeforeId($perPage, $lastId, $column);
        }

        public static function forPageAfterId($perPage = 15, $lastId = 0, $column = 'id')
        {
            
            return $instance->forPageAfterId($perPage, $lastId, $column);
        }

        public static function reorder($column = null, $direction = 'asc')
        {
            
            return $instance->reorder($column, $direction);
        }

        public static function reorderDesc($column)
        {
            
            return $instance->reorderDesc($column);
        }

        public static function union($query, $all = false)
        {
            
            return $instance->union($query, $all);
        }

        public static function unionAll($query)
        {
            
            return $instance->unionAll($query);
        }

        public static function lock($value = true)
        {
            
            return $instance->lock($value);
        }

        public static function lockForUpdate()
        {
            
            return $instance->lockForUpdate();
        }

        public static function sharedLock()
        {
            
            return $instance->sharedLock();
        }

        public static function beforeQuery($callback)
        {
            
            return $instance->beforeQuery($callback);
        }

        public static function applyBeforeQueryCallbacks()
        {
            
            $instance->applyBeforeQueryCallbacks();
        }

        public static function toSql()
        {
            
            return $instance->toSql();
        }

        public static function toRawSql()
        {
            
            return $instance->toRawSql();
        }

        public static function rawValue($expression, $bindings = [])
        {
            
            return $instance->rawValue($expression, $bindings);
        }

        public static function getCountForPagination($columns = [])
        {
            
            return $instance->getCountForPagination($columns);
        }

        public static function implode($column, $glue = '')
        {
            
            return $instance->implode($column, $glue);
        }

        public static function exists()
        {
            
            return $instance->exists();
        }

        public static function doesntExist()
        {
            
            return $instance->doesntExist();
        }

        public static function existsOr($callback)
        {
            
            return $instance->existsOr($callback);
        }

        public static function doesntExistOr($callback)
        {
            
            return $instance->doesntExistOr($callback);
        }

        public static function count($columns = '*')
        {
            
            return $instance->count($columns);
        }

        public static function min($column)
        {
            
            return $instance->min($column);
        }

        public static function max($column)
        {
            
            return $instance->max($column);
        }

        public static function sum($column)
        {
            
            return $instance->sum($column);
        }

        public static function avg($column)
        {
            
            return $instance->avg($column);
        }

        public static function average($column)
        {
            
            return $instance->average($column);
        }

        public static function aggregate($function, $columns = [])
        {
            
            return $instance->aggregate($function, $columns);
        }

        public static function numericAggregate($function, $columns = [])
        {
            
            return $instance->numericAggregate($function, $columns);
        }

        public static function insert($values)
        {
            
            return $instance->insert($values);
        }

        public static function insertOrIgnore($values)
        {
            
            return $instance->insertOrIgnore($values);
        }

        public static function insertGetId($values, $sequence = null)
        {
            
            return $instance->insertGetId($values, $sequence);
        }

        public static function insertUsing($columns, $query)
        {
            
            return $instance->insertUsing($columns, $query);
        }

        public static function insertOrIgnoreUsing($columns, $query)
        {
            
            return $instance->insertOrIgnoreUsing($columns, $query);
        }

        public static function updateFrom($values)
        {
            
            return $instance->updateFrom($values);
        }

        public static function updateOrInsert($attributes, $values = [])
        {
            
            return $instance->updateOrInsert($attributes, $values);
        }

        public static function incrementEach($columns, $extra = [])
        {
            
            return $instance->incrementEach($columns, $extra);
        }

        public static function decrementEach($columns, $extra = [])
        {
            
            return $instance->decrementEach($columns, $extra);
        }

        public static function truncate()
        {
            
            $instance->truncate();
        }

        public static function getColumns()
        {
            
            return $instance->getColumns();
        }

        public static function raw($value)
        {
            
            return $instance->raw($value);
        }

        public static function getBindings()
        {
            
            return $instance->getBindings();
        }

        public static function getRawBindings()
        {
            
            return $instance->getRawBindings();
        }

        public static function setBindings($bindings, $type = 'where')
        {
            
            return $instance->setBindings($bindings, $type);
        }

        public static function addBinding($value, $type = 'where')
        {
            
            return $instance->addBinding($value, $type);
        }

        public static function castBinding($value)
        {
            
            return $instance->castBinding($value);
        }

        public static function mergeBindings($query)
        {
            
            return $instance->mergeBindings($query);
        }

        public static function cleanBindings($bindings)
        {
            
            return $instance->cleanBindings($bindings);
        }

        public static function getProcessor()
        {
            
            return $instance->getProcessor();
        }

        public static function getGrammar()
        {
            
            return $instance->getGrammar();
        }

        public static function useWritePdo()
        {
            
            return $instance->useWritePdo();
        }

        public static function cloneWithout($properties)
        {
            
            return $instance->cloneWithout($properties);
        }

        public static function cloneWithoutBindings($except)
        {
            
            return $instance->cloneWithoutBindings($except);
        }

        public static function dump(...$args)
        {
            
            return $instance->dump(...$args);
        }

        public static function dumpRawSql()
        {
            
            return $instance->dumpRawSql();
        }

        public static function dd()
        {
            
            return $instance->dd();
        }

        public static function ddRawSql()
        {
            
            return $instance->ddRawSql();
        }

        public static function wherePast($columns)
        {
            
            return $instance->wherePast($columns);
        }

        public static function whereNowOrPast($columns)
        {
            
            return $instance->whereNowOrPast($columns);
        }

        public static function orWherePast($columns)
        {
            
            return $instance->orWherePast($columns);
        }

        public static function orWhereNowOrPast($columns)
        {
            
            return $instance->orWhereNowOrPast($columns);
        }

        public static function whereFuture($columns)
        {
            
            return $instance->whereFuture($columns);
        }

        public static function whereNowOrFuture($columns)
        {
            
            return $instance->whereNowOrFuture($columns);
        }

        public static function orWhereFuture($columns)
        {
            
            return $instance->orWhereFuture($columns);
        }

        public static function orWhereNowOrFuture($columns)
        {
            
            return $instance->orWhereNowOrFuture($columns);
        }

        public static function whereToday($columns, $boolean = 'and')
        {
            
            return $instance->whereToday($columns, $boolean);
        }

        public static function whereBeforeToday($columns)
        {
            
            return $instance->whereBeforeToday($columns);
        }

        public static function whereTodayOrBefore($columns)
        {
            
            return $instance->whereTodayOrBefore($columns);
        }

        public static function whereAfterToday($columns)
        {
            
            return $instance->whereAfterToday($columns);
        }

        public static function whereTodayOrAfter($columns)
        {
            
            return $instance->whereTodayOrAfter($columns);
        }

        public static function orWhereToday($columns)
        {
            
            return $instance->orWhereToday($columns);
        }

        public static function orWhereBeforeToday($columns)
        {
            
            return $instance->orWhereBeforeToday($columns);
        }

        public static function orWhereTodayOrBefore($columns)
        {
            
            return $instance->orWhereTodayOrBefore($columns);
        }

        public static function orWhereAfterToday($columns)
        {
            
            return $instance->orWhereAfterToday($columns);
        }

        public static function orWhereTodayOrAfter($columns)
        {
            
            return $instance->orWhereTodayOrAfter($columns);
        }

        public static function explain()
        {
            
            return $instance->explain();
        }

        public static function macro($name, $macro)
        {
            \Illuminate\Database\Query\Builder::macro($name, $macro);
        }

        public static function mixin($mixin, $replace = true)
        {
            \Illuminate\Database\Query\Builder::mixin($mixin, $replace);
        }

        public static function flushMacros()
        {
            \Illuminate\Database\Query\Builder::flushMacros();
        }

        public static function macroCall($method, $parameters)
        {
            
            return $instance->macroCall($method, $parameters);
        }

}
    class Event extends \Illuminate\Support\Facades\Event {}
    class File extends \Illuminate\Support\Facades\File {}
    class Gate extends \Illuminate\Support\Facades\Gate {}
    class Hash extends \Illuminate\Support\Facades\Hash {}
    class Http extends \Illuminate\Support\Facades\Http {}
    class Js extends \Illuminate\Support\Js {}
    class Lang extends \Illuminate\Support\Facades\Lang {}
    class Log extends \Illuminate\Support\Facades\Log {}
    class Mail extends \Illuminate\Support\Facades\Mail {}
    class Notification extends \Illuminate\Support\Facades\Notification {}
    class Number extends \Illuminate\Support\Number {}
    class Password extends \Illuminate\Support\Facades\Password {}
    class Process extends \Illuminate\Support\Facades\Process {}
    class Queue extends \Illuminate\Support\Facades\Queue {}
    class RateLimiter extends \Illuminate\Support\Facades\RateLimiter {}
    class Redirect extends \Illuminate\Support\Facades\Redirect {}
    class Request extends \Illuminate\Support\Facades\Request {}
    class Response extends \Illuminate\Support\Facades\Response {}
    class Route extends \Illuminate\Support\Facades\Route {}
    class Schedule extends \Illuminate\Support\Facades\Schedule {}
    class Schema extends \Illuminate\Support\Facades\Schema {}
    class Session extends \Illuminate\Support\Facades\Session {}
    class Storage extends \Illuminate\Support\Facades\Storage {}
    class Str extends \Illuminate\Support\Str {}
    class Uri extends \Illuminate\Support\Uri {}
    class URL extends \Illuminate\Support\Facades\URL {}
    class Validator extends \Illuminate\Support\Facades\Validator {}
    class View extends \Illuminate\Support\Facades\View {}
    class Vite extends \Illuminate\Support\Facades\Vite {}
}

