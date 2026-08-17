/**
 * BLOCK: chatty-mango-guten-dpfwt-reset
 *
 *
 * @package     Tag Groups Pro
 * @author      Christoph Amthor
 * @copyright   2018 Christoph Amthor (@ Chatty Mango, chattymango.com)
 */

import '../editor.css';

import Select from 'react-select';

import TagGroupsHelp from '../components/tag-groups-help';

const { __ } = wp.i18n;

const { createBlock, registerBlockType } = wp.blocks;

const { InspectorControls, PlainText } = wp.editor;

const { PanelBody } = wp.components;

const { Component, Fragment } = wp.element;

const {
  siteUrl,
  siteLang,
  pluginUrl,
  hasPremium,
  serverSideRender,
} = ChattyMangoTagGroupsGlobal;

const helpUrl = '';
const helpProduct = hasPremium ? 'tag-groups-premium' : 'tag-groups';
const helpFeature = 'toggle-post-filter/toggle-post-filter-gutenberg-block/';

class tagGroupsPremiumDPFParameters extends Component {
  // Method for setting the initial state.
  static getInitialState(attributes) {}

  // Constructing our component. With super() we are setting everything to 'this'.
  // Now we can access the attributes with this.props.attributes
  constructor() {
    super(...arguments);

    this.state = this.constructor.getInitialState(this.props.attributes);
  }

  render() {
    const { attributes, setAttributes } = this.props;

    const { button_class, button_text, cover, source, theme } = attributes;

    if (source !== 'gutenberg') {
      setAttributes({ source: 'gutenberg' });
    }

    let optionsTheme = [
      { value: '', label: 'none' },
      { value: 'light', label: 'light' },
      { value: 'dark', label: 'dark' },
    ];

    return [
      <InspectorControls key='inspector'>
        <div className='chatty-mango-inspector-control'>
          <PanelBody title={__('Labels and Messages')} initialOpen={false}>
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='button_text'
            />
            <label htmlFor='tg_input_button_text'>{__('Button text')}</label>
            <PlainText
              id='tg_input_button_text'
              className='input-control'
              value={button_text ? button_text : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(button_text) => setAttributes({ button_text })}
            />
          </PanelBody>

          <PanelBody title={__('Theme')} initialOpen={false}>
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='theme'
            />
            <label htmlFor='tg_input_theme'>{__('Theme')}</label>
            <Select
              id='tg_input_theme'
              onChange={(selected) => setAttributes({ theme: selected.value })}
              value={theme}
              options={optionsTheme}
              multi={false}
              closeOnSelect={true}
            />
          </PanelBody>

          <PanelBody title={__('Advanced Styling')} initialOpen={false}>
            <div>
              <TagGroupsHelp
                url={helpUrl}
                product={helpProduct}
                feature={helpFeature}
                siteLang={siteLang}
                topic='button_class'
              />
              <label htmlFor='tg_input_button_class'>{'button class'}</label>
            </div>
            <PlainText
              id='tg_input_button_class'
              className='input-control'
              value={button_class ? button_class : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(button_class) => setAttributes({ button_class })}
            />
          </PanelBody>
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
          <div className='chatty-mango-not-active-all'>
            <button
              className={button_class + ' tg_dpf_toggle_reset_button'}
              autocomplete='off'
              type='button'
            >
              {button_text}
            </button>
          </div>
        )}
        {!cover && !serverSideRender && (
          <div className='chatty-mango-editor'>
            <div className='chatty-mango-editor-block'>
            </div>
            <div className='chatty-mango-editor-block'>
              <h3 className='chatty-mango-editor-title'>
                {__('Toggle Post Filter - Reset Button')}
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
var cmTagGroupsDPFResetBlock = registerBlockType(
  'chatty-mango/chatty-mango-guten-dpfwt-reset',
  {
      title: __('Toggle Post Filter - Reset Button'),
      icon: 'filter', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
      category: 'chatty-mango',
      description: __(
        'Show a list of posts that corresponds to tags entered by your visitors.'
      ),
      keywords: [__('post filter'), __('reset'), 'Chatty Mango'],
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
                    cmTagGroupsDPFResetBlock.attributes[attribute] &&
                    attributes[attribute] !==
                      cmTagGroupsDPFResetBlock.attributes[attribute].default
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

              let text = '[tag_groups_tpf_reset ' + parameters.join(' ') + ']';
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
        button_class: {
          // configurable in block
          type: 'string',
          default: '',
        },
        button_text: {
          // configurable in block
          type: 'string',
          default: 'Reset filter',
        },
        theme: {
          // configurable in block
          type: 'string',
          default: 'light',
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
