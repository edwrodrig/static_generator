<?php
declare(strict_types=1);

namespace edwrodrig\static_generator\widget;

/**
 * Class ComponentFile
 * Convenience class where the content of the Component is retrieved from a file.
 *
 * @api
 * @package edwrodrig\static_generator\widget
 */
class ComponentFile extends Component
{
    /**
     * The filename with the content
     *
     * @var string
     */
    protected string $source_filename;

    /**
     * ComponentFile constructor.
     *
     * @api
     * @uses ComponentFile::setFilename() to set the filename
     * @param string $source_filename
     * @param null|string $replacement
     * @param string $pattern
     */
    public function __construct(string $source_filename, ?string $replacement = '', string $pattern = '@@@') {
        $this->setFilename($source_filename);
        parent::__construct($replacement, $pattern);
    }

    /**
     * Set the source filename with the content.
     *
     * @api
     * @param $source_filename
     * @return ComponentFile
     */
    public function setFilename($source_filename) : ComponentFile {
        $this->source_filename = $source_filename;
        return $this;
    }

    /**
     * Just echo the file.
     *
     * @api
     * @return mixed|void
     */
    public function content() {
        /** @noinspection PhpIncludeInspection */
        include $this->source_filename;

    }
}