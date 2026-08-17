/**
 * BLOCK: chatty-mango/chatty-mango-tpf-order-menu
 *
 *
 * @package     Tag Groups Pro
 * @author      Christoph Amthor
 * @copyright   2018 Christoph Amthor (@ Chatty Mango, chattymango.com)
 */

import '../editor.css';

import Select from 'react-select';

import TagGroupsServerSideRender from '../components/tag-groups-render';

import TagGroupsHelp from '../components/tag-groups-help';

const { __ } = wp.i18n;

const { createBlock, registerBlockType } = wp.blocks;

const { InspectorControls, PlainText } = wp.editor;

const { PanelBody, ToggleControl } = wp.components;

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
const helpFeature =
  'toggle-post-filter/toggle-post-filter-order-menu-gutenberg-block/';

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

    const {
      div_class,
      order_text,
      orderby_text,
      sumoselect,
      theme,
      cover,
      source,
    } = attributes;

    if (source !== 'gutenberg') {
      setAttributes({ source: 'gutenberg' });
    }

    let optionsTheme = [
      { value: '', label: 'none' },
      { value: 'light', label: 'light' },
      { value: 'dark', label: 'dark' },
    ];

    const renderAttributes = {
      div_class,
      order_text,
      orderby_text,
      sumoselect,
      theme,
    };

    return [
      <InspectorControls key='inspector'>
        <div className='chatty-mango-inspector-control'>
          <PanelBody title={__('Labels and Messages')} initialOpen={false}>
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='orderby_text'
            />
            <label htmlFor='tg_input_orderby_text'>
              {__('Text before "Order by" menu')}
            </label>
            <PlainText
              id='tg_input_orderby_text'
              className='input-control'
              value={orderby_text ? orderby_text : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(orderby_text) => setAttributes({ orderby_text })}
            />
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='order_text'
            />
            <label htmlFor='tg_input_order_text'>
              {__('Text before "Order" menu')}
            </label>
            <PlainText
              id='tg_input_order_text'
              className='input-control'
              value={order_text ? order_text : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(order_text) => setAttributes({ order_text })}
            />
            {/* <TagGroupsHelp url={helpUrl} product={helpProduct} feature={helpFeature} siteLang={siteLang} topic='orderby_options' />
            <label htmlFor='tg_input_orderby_options'>{__('Order By Options')}</label>
            <Select
              id='tg_input_orderby_options'
              onChange={(selected) => {setAttributes({ theme: selected.value }})}
              value={theme}
              options={optionsOrderby}
              multi={false}
              closeOnSelect={true}
            /> */}
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
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='sumoselect'
            />
            <ToggleControl
              label={__('Show select menus in flat design.')}
              checked={sumoselect}
              onChange={(value) => {
                setAttributes({ sumoselect: value ? 1 : 0 });
              }}
            />
          </PanelBody>

          <PanelBody title={__('Advanced Styling')} initialOpen={false}>
            <div>
              <TagGroupsHelp
                url={helpUrl}
                product={helpProduct}
                feature={helpFeature}
                siteLang={siteLang}
                topic='div_class'
              />
              <label htmlFor='tg_input_div_class'>{'div class'}</label>
            </div>
            <PlainText
              id='tg_input_div_class'
              className='input-control'
              value={div_class ? div_class : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(div_class) => setAttributes({ div_class })}
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
          <div>
            <TagGroupsServerSideRender
              block='chatty-mango/chatty-mango-tpf-order-menu'
              className='chatty-mango-not-active-all'
              attributes={renderAttributes}
            />
          </div>
        )}
        {!cover && !serverSideRender && (
          <div className='chatty-mango-editor'>
            <div className='chatty-mango-editor-block'>
            </div>
            <div className='chatty-mango-editor-block'>
              <h3 className='chatty-mango-editor-title'>
                {__('Toggle Post Filter - Order Button')}
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
var cmTagGroupsTPFOrderMenuBlock = registerBlockType(
  'chatty-mango/chatty-mango-tpf-order-menu',
  {
      title: __('Toggle Post Filter - Order Menu'),
      icon: 'filter', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
      category: 'chatty-mango',
      description: __(
        'Show a list of posts that corresponds to tags entered by your visitors.'
      ),
      keywords: [__('post filter'), __('sort order'), 'Chatty Mango'],
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
                    cmTagGroupsTPFOrderMenuBlock.attributes[attribute] &&
                    attributes[attribute] !==
                      cmTagGroupsTPFOrderMenuBlock.attributes[attribute].default
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
                '[tag_groups_tpf_order_menu ' + parameters.join(' ') + ']';
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
        div_class: {
          // configurable in block
          type: 'string',
          default: 'tg_dpf_order_menu',
        },
        order_text: {
          // configurable in block
          type: 'string',
          default: 'Order:',
        },
        orderby_text: {
          // configurable in block
          type: 'string',
          default: 'Order by:',
        },
        order_options: {
          // configurable in block
          type: 'string',
          default: 'desc:↓|asc:↑',
        },
        orderby_options: {
          // configurable in block
          type: 'string',
          default: 'date:date|author:author|title:title',
        },
        sumoselect: {
          // configurable in block
          type: 'integer',
          default: 1,
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
