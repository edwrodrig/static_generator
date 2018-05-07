<?php

namespace edwrodrig\static_generator;

use edwrodrig\static_generator\exception\InvalidTemplateClassException;
use edwrodrig\static_generator\util\FileData;
use edwrodrig\static_generator\util\Util;
use Exception;
use phpDocumentor\Reflection\DocBlockFactory;

class PagePhp extends Page
{
    /**
     * This page is processes as a template.
     */
    const MODE_TEMPLATE = 1;

    /**
     * This page is not processed in any way. Just save in an output file as it is.
     */
    const MODE_RAW = 2;

    /**
     * This page is processed as a php file but does not generated any output file unless they explicitly says it.
     */
    const MODE_SILENT = 3;


    private $mode = self::MODE_TEMPLATE;

    /**
     * @var string
     */
    private $template_class = Template::class;

    /**
     * The additional data of the php page.
     * @var array
     */
    private $data = [];

    /**
     * PagePhp constructor.
     * @param FileData $data
     * @param string $output_base_dir
     * @throws InvalidTemplateClassException
     */
    public function __construct(FileData $data, string $output_base_dir) {
        parent::__construct($data, $output_base_dir);

        $this->loadDataFromFirstComment();
    }


    /**
     * @throws InvalidTemplateClassException
     */
    private function loadDataFromFirstComment() {
        $tokens = token_get_all($this->input_file_data->getFileContents());
        foreach ($tokens as $token) {
            if ($token[0] !== T_COMMENT)
                continue;

            $content = $token[1];
            $factory = DocBlockFactory::createInstance();
            $reader = $factory->create($content);

            if ( $reader->hasTag('data') ) {
                $data = strval($reader->getTagsByName('data')[0]);
                $data = @json_decode($data, true);
                $this->data = $data;
            }

            if ( $reader->hasTag('raw') ) {
                $this->mode = self::MODE_RAW;
            } else if ( $reader->hasTag('silent') ) {
                $this->mode = self::MODE_SILENT;
            } else if ( $reader->hasTag('template') ) {
                $this->mode = self::MODE_TEMPLATE;

                $template_class = strval($reader->getTagsByName('template')[0]);

                if ( empty($template_class) ) {
                    $this->template_class = Template::class;
                }
                else if ( !class_exists($template_class) || !$template_class instanceof Template )  {
                    /** @noinspection PhpInternalEntityUsedInspection */
                    throw new InvalidTemplateClassException($template_class);
                } else {
                    $this->template_class = $template_class;
                }

            } else {
                $this->mode = self::MODE_TEMPLATE;
                $this->template_class = Template::class;
            }

            return;
        }

    }

    /**
     * If the php file processes it silently.
     * Without generating and output unless it is specified explicitly
     * @return bool
     */
    public function isSilent() : bool {
        return $this->mode == self::MODE_SILENT;
    }

    /**
     * If this file is processes as a template.
     * @return bool
     */
    public function isTemplate() : bool {
        return $this->mode == self::MODE_TEMPLATE;
    }

    /**
     * If this file is not processes.
     *
     * @return bool
     */
    public function isRaw() : bool {
        return $this->mode == self::MODE_RAW;
    }

    public function getRelativePath() : string
    {
        $relative_path = parent::getRelativePath();
        if ( $this->isTemplate() ) {

                $relative_path = preg_replace(
                    '/\.php$/',
                    '',
                    $relative_path
                );
        }

        return $relative_path;
    }

    public function getTemplate() : Template {
        return new $this->template_class($this);
    }

    /**
     * @throws Exception
     */
    public function generate()
    {
        $this->log(sprintf("Processing file [%s]...", $this->input_file_data->getRelativePath()));

        if ( $this->mode == self::MODE_TEMPLATE )
            $this->processTemplate();
        else if ( $this->mode == self::MODE_SILENT )
            $this->processSilent();
        else if ( $this->mode == self::MODE_RAW )
            $this->processRaw();

        $this->log("DONE\n");
    }

    /**
     * @throws Exception
     */
    private function processSilent() {
        self::push($this);

        Util::outputBufferSafe(function () {
            /** @noinspection PhpIncludeInspection */
            require($this->input_file_data->getAbsolutePath());
        });

        self::pop();
    }

    private function processRaw() {
        $content = file_get_contents($this->input_file_data->getAbsolutePath());
        $this->writePage($content);
    }

    /**
     * @throws Exception
     */
    private function processTemplate() {
        self::push($this);

        $content = Util::outputBufferSafe(function () {
            $this->getTemplate()->print();
        });

        $this->writePage($content);

        self::pop();


    }
}
