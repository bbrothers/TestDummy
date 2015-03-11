<?php namespace Laracasts\TestDummy;

use Laracasts\TestDummy\BuildableRepositoryInterface as BuildableRepository;

class Factory {

    /**
     * The path to the factories directory.
     *
     * @var string
     */
    public static $factoriesPath = 'tests/factories';

    /**
     * Exclude files from the factories path.
     *
     * @var array
     */
    private static $excludeByRegex = ['__MACOSX', '^\.'];

    /**
     * The user registered factories.
     *
     * @var array
     */
    private static $factories;

    /**
     * The persistence layer.
     *
     * @var BuildableRepositoryInterface
     */
    public static $databaseProvider;

    /**
     * Create a new factory instance.
     *
     * @param string $factoriesPath
     * @param BuildableRepository $databaseProvider
     */
    public function __construct($factoriesPath = null, BuildableRepository $databaseProvider = null)
    {
        $this->loadFactories($factoriesPath);
        $this->setDatabaseProvider($databaseProvider);
    }

    /**
     * Get the user registered factories.
     *
     * @return array
     */
    public function factories()
    {
        return static::$factories;
    }

    /**
     * Get the database provider.
     *
     * @return BuildableRepositoryInterface
     */
    public function databaseProvider()
    {
        return static::$databaseProvider;
    }

    /**
     * Fill an entity with test data, without saving it.
     *
     * @param string $name
     * @param array  $attributes
     * @return array
     */
    public static function build($name, array $attributes = [])
    {
        return (new static)->getBuilder()->build($name, $attributes);
    }

    /**
     * Fill and save an entity.
     *
     * @param string $name
     * @param array  $attributes
     * @return mixed
     */
    public static function create($name, array $attributes = [])
    {
        return (new static)->getBuilder()->create($name, $attributes);
    }

    /**
     * Set the number of times to create a record.
     *
     * @param $times
     * @return $this
     */
    public static function times($times)
    {
        return (new static)->getBuilder()->setTimes($times);
    }

    /**
     * Create a Builder instance.
     *
     * @return Builder
     */
    private function getBuilder()
    {
        return new Builder($this->databaseProvider(), $this->factories());
    }

    /**
     * Load the user provided factories.
     *
     * @param  string $factoriesPath
     * @return void
     */
    private function loadFactories($factoriesPath)
    {
        $factoriesPath = $factoriesPath ?: static::$factoriesPath;

        if ( ! static::$factories)
        {
            static::$factories = (new FactoriesLoader)->load($factoriesPath);
        }
    }

    /**
     * Set the database provider for the data generation.
     *
     * @param  BuildableRepository $provider
     * @return void
     */
    private function setDatabaseProvider($provider)
    {
        if ( ! static::$databaseProvider)
        {
            static::$databaseProvider = $provider ?: new EloquentDatabaseProvider;
        }
    }

    /**
     * Get exclusion rules.
     *
     * @return array
     */
    public static function getExclusionFilters()
    {

        return self::$excludeByRegex;
    }

    /**
     * Add an exclusion rule.
     *
     * @param $exclude
     */
    public static function addExclusionFilter($exclude)
    {

        $exclude = self::parseExclusionRules($exclude);

        static::$excludeByRegex = array_merge(static::$excludeByRegex, $exclude);
    }

    /**
     * Override all exclusion rules.
     *
     * @param $exclude
     */
    public static function setExclusionFilters($exclude)
    {

        $exclude = self::parseExclusionRules($exclude);

        static::$excludeByRegex = $exclude;
    }

    /**
     * Parse input rules to an array.
     *
     * @param $exclude
     * @return array
     */
    protected static function parseExclusionRules($exclude)
    {

        if (is_string($exclude))
        {
            $exclude = preg_split('/ ?, ?/', $exclude);
        }

        if (! self::assertIsTraversable($exclude)) {
            throw new \InvalidArgumentException(
                'Rules should be array accessible or comma separated string of rules.'
            );
        }

        return (array) $exclude;
    }

    /**
     * Assert a value is an array or Traversable
     *
     * @param $value
     * @return bool
     */
    protected static function assertIsTraversable($value)
    {

        return is_array($value) or $value instanceof \Traversable;
    }

}
