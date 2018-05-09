<?php
declare(strict_types=1);

namespace edwrodrig\static_generator\widget;

/**
 * Class Component
 *
 * In web context (html, js, css) is difficult to reutilize code.
 * One of the solutions is to use different ids for each instance of a chunk of code.
 * This abstract class is a helper for this situation.
 * You can have some chuck of code and mark it with some {@see Component::$pattern pattern}
 * and replace it with some {@see Component::$replacement}
 * @api
 * @package edwrodrig\static_generator\widget
 */
abstract class Component
{
    /**
     * The pattern that will be replaced with {@see Component::$prefix the prefix}
     * @see Component::setPattern()
     * @var string
     */
    protected $pattern;

    /**
     * The prefix that will replace the {@see Component::$prefix_pattern prefix pattern}
     * @var string
     * @see Component::setReplacement()
     */
    protected $replacement;

    /**
     * Component constructor.
     * @api
     * @param null|string $replacement The {@see Component::setReplacement() replacement}
     * @param string $pattern The {@see Component::$pattern pattern}
     * @uses Component::setReplacement() For set the replacement
     */
    public function __construct(?string $replacement = '', string $pattern = '@@@')
    {
        $this->setPattern($pattern);
        $this->setReplacement($replacement);
    }

    /**
     * Set the {@see Component::$replacement replacement}
     *
     * If the replacement is empty then generates one with {@see uniqid()}
     * @api
     * @param null|string $replacement
     * @return $this
     */
    public function setReplacement(?string $replacement) : Component {
        if ( empty($replacement) )
            $replacement = uniqid();

        $this->replacement = $replacement;
        return $this;
    }

    /**
     * Set the {@see Component::$pattern pattern }
     *
     * @api
     * @param string $pattern
     * @return $this
     */
    public function setPattern(string $pattern) : Component {
        $this->pattern = $pattern;
        return $this;
    }

    /**
     * If the {@see Component::$pattern pattern} is valid for replacement.
     *
     * @internal
     * @return bool
     */
    private function isPatternReplaceable() : bool {
        return !empty($this->pattern);
    }

    /**
     * This function echoes the output with the replacement.
     *
     * You may override if you want to change its behaviour.
     * @api
     * @uses Component::content() To retrieve the content
     */
    public function print() {
        ob_start();
        $this->content();
        $content = ob_get_clean();

        if ( $this->isPatternReplaceable() ) {
            $content = str_replace(
                $this->pattern,
                $this->replacement,
                $content
            );
        }

        echo $content;
    }

    /**
     * This function should echo all output that the component will generate.
     *
     * This is the output that the replacement will be applied.
     * @api
     * @return mixed
     */
    abstract public function content();

}

