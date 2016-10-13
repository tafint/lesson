<?php
namespace Core;

/**
 * This is a class View
 */
class View
{   
    /** @var array $_content contains content to body view */
    protected $_content = array();

    /** @var array $_content contains content to before body view */
    protected $_before_content = array();

    /** @var array $_content contains content to after body view */
    protected $_after_content = array();

    /**
     * load content for body view and save to $_content.
     *
     * @param string $view is file view in folder view.
     *
     * @param string $data contain variable need extract.
     *
     */
    public function load_content($view, $data = array())
    {   
        if (preg_match("/\./", $view)) {
            $view = explode(".", $view);
            $view = implode("/", $view);
        }

        extract($data);

        ob_start();
        require_once PATH . "/view/$view.php";
        $content = ob_get_contents();
        ob_end_clean();

        $this->_content[] = $content;
    }

    /**
     * load content for before body view and save to $_content.
     *
     * @param string $view is file view in folder view.
     *
     * @param string $data contain variable need extract.
     *
     */
    public function load_template_before($view, $data = array())
    {
        extract($data);
        ob_start();
        require_once PATH . "/view/template/$view.php";
        $content = ob_get_contents();
        ob_end_clean();

        $this->_before_content[] = $content;
    }

    /**
     * load content for after body view and save to $_content.
     *
     * @param string $view is file view in folder view.
     *
     * @param string $data contain variable need extract.
     *
     */
    public function load_template_after($view, $data = array())
    {
        extract($data);
        ob_start();
        require_once PATH . "/view/template/$view.php";
        $content = ob_get_contents();
        ob_end_clean();

        $this->_after_content[] = $content;
    }

    /**
     * show all.
     *
     */
    public function show()
    {   
        foreach ($this->_before_content as $before_content) {
            echo $before_content;
        }
        foreach ($this->_content as $content) {
            echo $content;
        }
        foreach ($this->_after_content as $after_content) {
            echo $after_content;
        }
    }

    /**
     * reset all property.
     *
     */
    public function reset()
    {   
        $this->_content = array();

        $this->_before_content = array();

        $this->_after_content = array();
    }
}