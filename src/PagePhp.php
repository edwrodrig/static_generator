<?php
declare(strict_types=1);

namespace edwrodrig\static_generator;

use edwrodrig\static_generator\exception\InvalidTemplateClassException;
use edwrodrig\static_generator\template\Template;
use edwrodrig\static_generator\util\FileData;
use edwrodrig\static_generator\util\Util;
use Exception;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Type;

/**
 * Class PagePhp
 * When annotate PhpFiles ensure that the annotations are at one space from initial * or begin of line, in other case it will be parsed incorrectly.
 * @package edwrodrig\static_generator
 */
class PagePhp extends PageFile
{
    /**
     * This page is processes as a {@see PagePhp::processTemplate() template}.
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
     * @param string $source_path
     * @param Context $context The generation context
     * @throws InvalidTemplateClassException
     */
    public function __construct(string $source_path, Context $context) {
        parent::__construct($source_path, $context);

        if ( $doc_block = $this->getDocBlock() ) {
            $this->loadDataFromDoc($doc_block);
            $this->loadTypeDataFromDoc($doc_block);
        }
    }


    private function loadDataFromDoc(DocBlock $doc_block) {
        if ( $doc_block->hasTag('data') ) {
            $data = strval($doc_block->getTagsByName('data')[0]);
            $data = @json_decode($data, true);
            $this->data = $data;
        }
    }

    /**
     * @param DocBlock $doc_block
     * @throws InvalidTemplateClassException
     */
    private function loadTypeDataFromDoc(DocBlock $doc_block){
        if ( $doc_block->hasTag('raw') ) {
            $this->mode = self::MODE_RAW;

        } else if ( $doc_block->hasTag('silent') ) {
            $this->mode = self::MODE_SILENT;

        } else if ( $doc_block->hasTag('template') ) {
            $this->mode = self::MODE_TEMPLATE;

            $template_class = '';//strval($doc_block->getTagsByName('template')[0]);

            $vars = $doc_block->getTagsByName('var');
            /** @var $var DocBlock\Tags\Var_ */
            foreach ( $vars as $var ) {

                if ( $var->getVariableName() == 'this' ) {
                    if ( $description = $var->getDescription() ) {
                        /** @var $type DocBlock\Description*/
                        $template_class = strval($description);
                        break;
                    }
                }
            }

            if ( empty($template_class) || $template_class == Template::class ) {
                $this->template_class = Template::class;

            } else if ( class_exists($template_class) && is_subclass_of($template_class,Template::class) )  {
                $this->template_class = $template_class;

            } else {
                /** @noinspection PhpInternalEntityUsedInspection */
                throw new InvalidTemplateClassException($template_class);

            }

        } else {
            $this->mode = self::MODE_TEMPLATE;
            $this->template_class = Template::class;
        }
    }

    /**
     * Get the first Documentation block of the file.
     *
     * In the first comment is where al annotations for templating would be. Other Doc comments are ignored.
     * It is used when in the template you want to retrieve further information for the template (Example: {@see TemplateJs})
     * @api
     * @return null|DocBlock
     */
    public function getDocBlock() : ?DocBlock {
        $tokens = token_get_all($this->getSourceFileContents());
        foreach ($tokens as $token) {
            if ($token[0] !== T_COMMENT && $token[0] !== T_DOC_COMMENT)
                continue;

            $content = $token[1];
            $factory = DocBlockFactory::createInstance();
            return $factory->create($content);
        }
        return null;
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

    public function getTargetRelativePath() : string
    {
        $relative_path = parent::getTargetRelativePath();
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
     * Data provided in the {@see PagePhp::getDocBlock() first comment}
     *
     * This data can be used in different context specially in {@see Template::getData() templates}
     * Example
     * ```
     * @data something_in_json_format
     * ```
     * @data
     * @return array
     */
    public function getData() : array {
        return $this->data;
    }

    /**
     * @throws Exception
     */
    public function generate() : string
    {
        $this->getLogger()->begin(sprintf("Processing file [%s]...", $this->getSourceRelativePath()));

        $content = '';
        if ( $this->mode == self::MODE_TEMPLATE )
            $content = $this->processTemplate();
        else if ( $this->mode == self::MODE_SILENT )
            $content = $this->processSilent();
        else if ( $this->mode == self::MODE_RAW )
            $content = $this->processRaw();

        $this->getLogger()->end("DONE\n", false);

        return $content;
    }

    /**
     * @throws Exception
     */
    private function processSilent() : string {
        $content = Util::outputBufferSafe(function () {
            /** @noinspection PhpIncludeInspection */
            require($this->getSourceAbsolutePath());
        });
        return $content;
    }

    private function processRaw() : string {
        $content = $this->getSourceFileContents();
        $this->writePage($content);
        return $content;
    }

    /**
     * Process this file as a Template
     * @throws Exception
     */
    private function processTemplate() : string {

        $content = Util::outputBufferSafe(function () {
            $this->getTemplate()->print();
        });

        $this->writePage($content);

        return $content;
    }

    /**
     * Generates a page from function.
     *
     * Useful when php scripts generates a set of files like post entries.
     * This function is mean to be used {@see PagePhp::isSilent() silent} php pages but is ok to use in other php pages
     *
     * @uses PageFunction
     * @param string $relative_path The path relative to the {@see Context::getTargetRelativePath() target path}
     * @param callable $function The funciton that echo the content of the file
     * @throws \Exception
     */
    public function generateFromFunction(string $relative_path, callable $function)
    {
        $page = new PageFunction($relative_path, $this->context, $function);
        $page->generate();
    }
}
