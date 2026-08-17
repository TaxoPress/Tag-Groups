/**
 * BLOCK: chatty-mango-guten-dpfwt-messages
 *
 *
 * @package     Tag Groups Pro
 * @author      Christoph Amthor
 * @copyright   2018 Christoph Amthor (@ Chatty Mango, chattymango.com)
 */

import '../editor.css';

import TagGroupsHelp from '../components/tag-groups-help';

const { __ } = wp.i18n;

const { createBlock, registerBlockType } = wp.blocks;

const { InspectorControls } = wp.editor;

const { Component, Fragment } = wp.element;

const {
  siteLang,
  pluginUrl,
  hasPremium,
  serverSideRender,
} = ChattyMangoTagGroupsGlobal;

const helpUrl = '';
const helpProduct = hasPremium ? 'tag-groups-premium' : 'tag-groups';
const helpFeature = 'toggle-post-filter/toggle-post-filter-gutenberg-block/';

class tagGroupsPremiumDPFParameters extends Component {
  // Constructing our component. With super() we are setting everything to 'this'.
  // Now we can access the attributes with this.props.attributes
  constructor() {
    super(...arguments);
  }

  render() {
    const { attributes, setAttributes } = this.props;

    const { cover, source } = attributes;

    if (source !== 'gutenberg') {
      setAttributes({ source: 'gutenberg' });
    }

    return [
      <InspectorControls key='inspector'>
        <div className='chatty-mango-inspector-control'>
          {typeof tagGroupsTPFMenuLoaded !== 'undefined' &&
            !tagGroupsTPFMenuLoaded && (
              <div className='chatty-mango-help-transform chatty-mango-alert'>
                <div
                  dangerouslySetInnerHTML={{
                    __html: __(
                      'For this block you will also need the <b>Toggle Post Filter - Menu</b> block.'
                    ),
                  }}
                ></div>
              </div>
            )}
        </div>
      </InspectorControls>,
      <div>
        {!!cover && (
          <Fragment>
            <img src={pluginUrl + '/assets/images/features/' + cover} />
          </Fragment>
        )}
        {!cover && serverSideRender && (
          <div>
            <div id='tg_filter_dpf_toggle_box_messages_wrapper'>
              <div id='tg_filter_dpf_toggle_box_messages'>
                <div id='tg_filter_box_amount'>{__('1 post found.')}</div>
              </div>
            </div>
            <div style={{ clear: 'both' }}></div>
          </div>
        )}
        {!cover && !serverSideRender && (
          <div className='chatty-mango-editor'>
            <div className='chatty-mango-editor-block'>
            </div>
            <div className='chatty-mango-editor-block'>
              <h3 className='chatty-mango-editor-title'>
                {__('Toggle Post Filter - Message Field')}
              </h3>
              <div className='cm-gutenberg dashicons-before dashicons-admin-generic'>
                {__(
                  'Select this block and customize the filter in the Inspector.'
                )}
              </div>
              <div className='cm-gutenberg dashicons-before dashicons-warning'>
                {__('Please also add the Menu block and the Posts block.')}
              </div>
            </div>
          </div>
        )}
      </div>,
    ];
  }
}

/**
 * Register: a Gutenberg Block.
 *
 * @param  {string}	  name	   Block name.
 * @param  {Object}	  settings Block settings.
 * @return {?WPBlock}		   The block, if it has been successfully
 *							   registered; otherwise `undefined`.
 */
var cmTagGroupsDPFMessagesBlock = registerBlockType(
  'chatty-mango/chatty-mango-guten-dpfwt-messages',
  {
      title: __('Toggle Post Filter - Message Field'),
      icon: 'filter', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
      category: 'chatty-mango',
      description: __(
        'Show a list of posts that corresponds to tags entered by your visitors.'
      ),
      keywords: [__('post filter'), __('messages'), 'Chatty Mango'],
      transforms: {
        to: [
          {
            type: 'block',
            blocks: ['core/shortcode'],
            transform: function (attributes) {
              let parameters = [];
              for (var attribute in attributes) {
                if (attributes.hasOwnProperty(attribute)) {
                  if (
                    null !== attributes[attribute] &&
                    '' !== attributes[attribute] &&
                    'source' !== attribute &&
                    cmTagGroupsDPFMessagesBlock.attributes[attribute] &&
                    attributes[attribute] !==
                      cmTagGroupsDPFMessagesBlock.attributes[attribute].default
                  ) {
                    if (typeof attributes[attribute] === 'number') {
                      parameters.push(attribute + '=' + attributes[attribute]);
                    } else {
                      if (attributes[attribute].indexOf('"') === -1) {
                        parameters.push(
                          attribute + '="' + attributes[attribute] + '"'
                        );
                      } else {
                        parameters.push(
                          attribute + "='" + attributes[attribute] + "'"
                        );
                      }
                    }
                  }
                }
              }

              let text =
                '[tag_groups_tpf_messages ' + parameters.join(' ') + ']';
              return createBlock('core/shortcode', {
                text,
              });
            },
          },
        ],
      },
      supports: {
        html: false,
        customClassName: false,
        multiple: false,
      },
      example: {
        attributes: {
          cover: 'dynamic-post-filter-toggles.png',
        },
      },
      /**
       * Attributes are the same as shortcode parameters
       **/
      attributes: {
        cover: {
          type: 'string',
          default: '',
        },
        source: {
          // internal indicator to identify Gutebergb blocks
          type: 'string',
          default: '',
        },
      },

      /**
       * Composing and rendering the editor content and control elements
       */
      edit: tagGroupsPremiumDPFParameters,

      /**
       * We don't render any HTML when saving
       */
      save: function (props) {
        return null;
      },
  }
);
