<?php

// phpcs:disable Squiz.Scope.MethodScope.Missing -- Visibility added where needed

/**
* Tag Groups Pro
*
* @package     Tag Groups Pro
* @author      Christoph Amthor
* @copyright   2017 Christoph Amthor (@ Chatty Mango, chattymango.com)
* @license     see official vendor website
*
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
* THE SOFTWARE.
*
*/

if (! class_exists('TagGroups_Templates')) {
    class TagGroups_Templates
    {
          private $templates;
        private $templates_names;
        public function __construct()
        {

              $this->templates_names = array(
            __('Default', 'tag-groups'),
            __('Image Card', 'tag-groups'),
            __('Large Image', 'tag-groups')
              );
              $this->load();
        }


      /**
       * Load the properties of each template into an array
       *
       * @phpunit
       * @return void
       */
        private function load()
        {

            $this->templates = array();
            foreach ($this->templates_names as $template_name) {
                $view = new TagGroups_View('templates/' . sanitize_title($template_name));
                $this->templates[] = array(
                'label' => $template_name,
                'html'  => $view->return_html(),
                'image' => TAG_GROUPS_PLUGIN_URL .  '/assets/images/' . sanitize_title($template_name) . '-template.png',
                );
            }
        }


      /**
       * Getter for $this->templates
       *
       * @phpunit
       * @return array
       */
        public function get()
        {

            return $this->templates;
        }


      /**
       * Returns the default template array
       *
       * @phpunit
       * @return array
       */
        public function get_default()
        {

            return $this->templates[0];
        }


      /**
       * Returns the HTML of the default template
       *
       * @phpunit
       * @return string
       */
        public function get_html_of_default()
        {

            return $this->templates[0]['html'];
        }
    }


}
