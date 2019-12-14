<?php
declare(strict_types=1);

namespace edwrodrig\static_generator;

use edwrodrig\static_generator\exception\CopyException;
use edwrodrig\static_generator\exception\InvalidTemplateClassException;
use edwrodrig\static_generator\template\Template;
use edwrodrig\static_generator\util\Util;
use Exception;
use edwrodrig\static_generator\exception\InvalidTemplateMetadataException;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;
use Throwable;

/**
 * Class PagePhp
 * When annotate PhpFiles ensure that the annotations are at one space from initial * or begin of line, in other case it will be parsed incorrectly.
 * @api
 * @package edwrodrig\static_generator
 */
class PagePhp extends PageFile
{
    /**
     * This page is processes as a {@see PagePhp::processTemplate() template}.
     */
    const MODE_TEMPLATE = 1;

    /**
     * This page is not processed in any way. Just save in an {@see PagePhp::processRaw() output file as it is}.
     */
    const MODE_RAW = 2;

    /**
     * This page is processed as a php file but {@see PagePhp::processSilent() does not generated} any output file unless they explicitly says it.
     */
    const MODE_SILENT = 3;


    /**
     * The current mode of the page.
     * @see PagePhp::MODE_TEMPLATE default mode
     * @see PagePhp::MODE_RAW just copy mode
     * @see PagePhp::MODE_SILENT process but not generate target
     * @var int
     */
    private int $mode = self::MODE_TEMPLATE;

    /**
     * @var string
     */
    private string $template_class = Template::class;

    /**
     * The additional data of the php page.
     *
     * This is contained in the data annotation.
     * The content of the data anotation must be a valid json string.
     * @var array
     */
    private array $data = [];

    /**
     * PagePhp constructor.
     *
     * @api
     * @param string $source_path
     * @param Context $context The generation context
     * @throws InvalidTemplateClassException
     * @throws InvalidTemplateMetadataException
     */
    public function __construct(string $source_path, Context $context) {
        parent::__construct($source_path, $context);

        if ( $doc_block = $this->getDocBlock() ) {
            $this->loadDataFromDoc($doc_block);
            $this->loadTypeDataFromDoc($doc_block);
            $this->loadTemplateDataFromDoc($doc_block);
        }
    }


    /**
     * Load the data annotation
     *
     * @see PagePhp::$data
     * @param DocBlock $doc_block
     * @throws InvalidTemplateMetadataException
     */
    private function loadDataFromDoc(DocBlock $doc_block) {
        if ( $doc_block->hasTag('data') ) {
            $data = strval($doc_block->getTagsByName('data')[0]);
            $parsed_data = @json_decode($data, true);
            if ( is_null($parsed_data) ) {
                throw new InvalidTemplateMetadataException($data);
            }
            $this->data = $parsed_data;
        }
    }

    /**
     * Parse template annotation
     *
     * Determine the template class of the processing
     * @param DocBlock $doc_block
     * @throws InvalidTemplateClassException
     */
    private function loadTemplateDataFromDoc(DocBlock $doc_block)
    {
        $template_class = '';

        $vars = $doc_block->getTagsByName('var');
        /** @var $var DocBlock\Tags\Var_ */
        foreach ($vars as $var) {

            if ($var->getVariableName() == 'this') {
                if ($description = $var->getDescription()) {
                    /** @var $type DocBlock\Description */
                    $template_class = strval($description);
                    $template_class = preg_replace("/^\\\\/", '', $template_class);
                    break;
                }
            }
        }

        if (empty($template_class) || $template_class == Template::class) {
            $this->template_class = Template::class;

        } else if (class_exists($template_class) && is_subclass_of($template_class, Template::class)) {
            $this->template_class = $template_class;

        } else {
            throw new InvalidTemplateClassException($template_class);

        }
    }

    /**
     * Parse type annotations.
     *
     * Type annotations determine the type of processing of the php file.
     * @see PagePhp::$mode
     * @param DocBlock $doc_block
     */
    private function loadTypeDataFromDoc(DocBlock $doc_block){
        if ( $doc_block->hasTag('raw') ) {
            $this->mode = self::MODE_RAW;

        } else if ( $doc_block->hasTag('silent') ) {
            $this->mode = self::MODE_SILENT;

        } else if ( $doc_block->hasTag('template') ) {
            $this->mode = self::MODE_TEMPLATE;

        } else {
            $this->mode = self::MODE_TEMPLATE;
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
     * @api
     * @return bool
     */
    public function isSilent() : bool {
        return $this->mode == self::MODE_SILENT;
    }

    /**
     * If this file is processes as a template.
     *
     * Generally is the default mode or when the template annotation is present
     * @api
     * @return bool
     */
    public function isTemplate() : bool {
        return $this->mode == self::MODE_TEMPLATE;
    }

    /**
     * If this file is not processes.
     *
     * When the raw annotation is present the file is just copied
     * @api
     * @return bool
     */
    public function isRaw() : bool {
        return $this->mode == self::MODE_RAW;
    }

    /**
     * Get the target relative path.
     *
     * In the case of php files if it is a {@see PagePhp::isTemplate() template}, the last .php from the file is removed.
     * @api
     * @return string
     */
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

    /**
     * Get the template class.
     *
     * Only use this when the {@see PagePhp::$mode mode} is {@see PhpPage::isTemplate() template}.
     * In other case this may(should) fail.
     * @api
     * @return Template
     */
    public function getTemplate() : Template {
        return new $this->template_class($this);
    }

    /**
     * Data provided in the {@see PagePhp::getDocBlock() first comment}
     *
     * This data can be used in different context specially in {@see Template::getData() templates}
     * @api
     * @uses PagePhp::$data
     * @return array
     */
    public function getData() : array {
        return $this->data;
    }

    /**
     * Generates the output of the file
     *
     * The generation is according the {@see PagePhp::$mode mode}
     * @api
     * @throws Exception
     * @return string
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
     * Process the file silently.
     *
     * Does not generate a {@see Page::writeFile() file}, but the output is returned as a string.
     * @return string
     * @throws Throwable
     * @see PagePhp::isSilent()
     */
    private function processSilent() : string {

        return Util::outputBufferSafe(function () {
            $this->getTemplate()->print();
        });
    }

    /**
     * Process as a raw file
     *
     * When is considered a raw file then this page is {@see Page::copyPage() copied}, works the same as {@see PageCopy}
     * @see PagePhp::isRaw()
     * @return string
     * @throws CopyException
     */
    private function processRaw() : string {
        $this->copyPage();
        return '';
    }

    /**
     * Process this file as a Template.
     *
     * The file {@see Page::writePage() generates an output} but the content generation is delegated to the {@see PagePhp::getTemplate() template}
     * @see PagePhp::isTemplate()
     * @return string
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
     * @param string $relative_path The path relative to the {@see Context::getTargetRelativePath() target path}
     * @param callable $function The funciton that echo the content of the file
     * @return string
     * @throws Exception
     *@api
     * @uses PageFunction
     */
    public function generateFromFunction(string $relative_path, callable $function) : string
    {
        $page = new PageFunction($relative_path, $this->context, $function);
        return $page->generate();
    }
}
