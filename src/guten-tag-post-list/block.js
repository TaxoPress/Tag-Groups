/**
 * BLOCK: tag-groups-premium-post-filter
 *
 *
 * @package     Tag Groups Pro
 * @author      Christoph Amthor
 * @copyright   2019 Christoph Amthor (@ Chatty Mango, chattymango.com)
 * @since       1.18.0
 */

//	Import CSS.
// import '../style.scss';
import '../editor.css';

import Select from 'react-select';

import TagGroupsServerSideRender from '../components/tag-groups-render';

import TagGroupsHelp from '../components/tag-groups-help';

import {
  encodeTemplate,
  decodeTemplate,
  getIncludeOptions,
  getTaxonomyOptions,
  optionsOrderbyPosts,
  optionsOrder,
} from '../modules/functions';

import { getGroupsFromApi, getTaxonomiesFromApi } from '../modules/api';

import {
  TagGroupsIcon,
  HorizontalRuler,
  ClearFloats,
} from '../components/general';

import {
  handleChangeInclude,
  handleChangeTaxonomy,
  toggleOptionDisplayAmount,
} from '../modules/handlers';

const { __ } = wp.i18n;

const { createBlock, registerBlockType } = wp.blocks;

const { InspectorControls, PlainText } = wp.editor;

const { PanelBody, ToggleControl, RangeControl } = wp.components;

const { Component, Fragment } = wp.element;

const {
  siteUrl,
  siteLang,
  pluginUrl,
  hasPremium,
  templates,
  serverSideRender,
} = ChattyMangoTagGroupsGlobal;

const helpUrl = '';
const helpProduct = hasPremium ? 'tag-groups-premium' : 'tag-groups';
const helpFeature = 'post-list/post-list-gutenberg-block/';

class editFunction extends Component {
  // Method for setting the initial state.
  static getInitialState(attributes) {
    let selectedInclude = []; // empty means all
    let selectedTaxonomies = ['post_tag'];
    let uniqueId =
      'tag_groups_render_' + Math.random().toString(36).substring(7);

    // We need arrays for the select elements.
    if (attributes.include) {
      selectedInclude = attributes.include.split(',').map((x) => {
        return parseInt(x, 10);
      });
    }

    if (attributes.taxonomy) {
      selectedTaxonomies = attributes.taxonomy.split(',');
    }

    return {
      taxonomies: [],
      groups: [],
      selectedInclude,
      selectedTaxonomies,
      uniqueId,
    };
  }

  // Constructing our component. With super() we are setting everything to 'this'.
  // Now we can access the attributes with this.props.attributes
  constructor() {
    super(...arguments);

    const { attributes, setAttributes } = this.props;

    this.groupsEndPoint = '/tag-groups/v1/groups';
    this.taxonomiesEndPoint = '/tag-groups/v1/taxonomies';

    this.state = this.constructor.getInitialState(this.props.attributes);

    attributes.operator &&
      setAttributes({
        operator: attributes.operator.toUpperCase(),
      });

    if (attributes.template) {
      const decodedTemplate = decodeTemplate(attributes.template);
      if (decodedTemplate === attributes.template) {
        setAttributes({ template: encodeTemplate(attributes.template) });
      }
    }

    // Load data from REST API.
    getGroupsFromApi(this);
    getTaxonomiesFromApi(this);
  }

  render() {
    const { attributes, setAttributes } = this.props;

    const {
      article_class,
      caching_time,
      cover,
      default_image_src,
      display_amount,
      div_class,
      div_id,
      message_amount_plural,
      message_amount_singular,
      message_nothing_found,
      message_load_more,
      message_go_back,
      operator,
      order,
      orderby,
      pager,
      pager_position,
      posts_per_page,
      template,
    } = attributes;

    const decodedTemplate = decodeTemplate(template);

    if (attributes.source !== 'gutenberg') {
      setAttributes({ source: 'gutenberg' });
    }

    let renderAttributes = { ...attributes };
    renderAttributes.div_id = this.state.uniqueId;
    delete renderAttributes.cover;

    return [
      <InspectorControls key='inspector'>
        <div className='chatty-mango-inspector-control'>
          <PanelBody title={__('Tags and Taxonomies')} initialOpen={false}>
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='taxonomy'
            />
            <label htmlFor='tg_input_taxonomy'>
              {__('Include taxonomies')}
            </label>
            <Select
              id='tg_input_taxonomy'
              onChange={(options) => handleChangeTaxonomy(this, options)}
              value={this.state.selectedTaxonomies}
              options={getTaxonomyOptions(this)}
              multi={true}
              closeOnSelect={false}
              removeSelected={true}
            />
          </PanelBody>

          <PanelBody title={__('Groups')} initialOpen={false}>
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='include'
            />
            <label htmlFor='tg_input_include'>{__('Include groups')}</label>
            <Select
              id='tg_input_include'
              onChange={(options) => handleChangeInclude(this, options)}
              value={this.state.selectedInclude}
              options={getIncludeOptions(this)}
              multi={true}
              closeOnSelect={false}
              removeSelected={true}
            />
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='operator'
            />
            <label htmlFor='tg_input_operator'>{__('Logic Operator')}</label>
            <Select
              id='tg_input_operator'
              onChange={(option) => {
                if (option) setAttributes({ operator: option.value });
              }}
              value={operator && typeof operator === 'string' ? operator : 'IN'}
              options={[
                { value: 'IN', label: __('any group') },
                { value: 'AND', label: __('every groups') },
              ]}
            />
          </PanelBody>

          <PanelBody title={__('Posts')} initialOpen={false}>
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='posts_per_page'
            />
            <RangeControl
              label={__('Posts per page')}
              value={posts_per_page !== undefined ? Number(posts_per_page) : 22}
              onChange={(posts_per_page) => setAttributes({ posts_per_page })}
              min={1}
              max={100}
            />
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='pager'
            />
            <label htmlFor='tg_input_pager'>{__('Use a pager.')}</label>
            <Select
              id='tg_input_pager'
              onChange={(option) => {
                if (option) setAttributes({ pager: option.value });
              }}
              value={pager || 0}
              options={[
                { value: 0, label: __('off') },
                { value: 1, label: __('next and previous') },
                { value: 2, label: __('page numbers') },
              ]}
            />
            {!!pager && (
              <Fragment>
                <TagGroupsHelp
                  url={helpUrl}
                  product={helpProduct}
                  feature={helpFeature}
                  siteLang={siteLang}
                  topic='pager_position'
                />
                <label htmlFor='tg_input_pager_position'>
                  {__('Pager position')}
                </label>
                <Select
                  id='tg_input_pager_position'
                  onChange={(option) => {
                    if (option) setAttributes({ pager_position: option.value });
                  }}
                  value={pager_position || 'bottom'}
                  options={[
                    { value: 'top', label: __('top') },
                    { value: 'bottom', label: __('bottom') },
                    { value: 'both', label: __('both') },
                  ]}
                />
              </Fragment>
            )}
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='caching_time'
            />
            <RangeControl
              label={__('Server caching time (minutes)')}
              value={caching_time !== undefined ? Number(caching_time) : 10}
              onChange={(caching_time) => setAttributes({ caching_time })}
              min={0}
              max={600}
            />
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='orderby'
            />
            <label htmlFor='tg_input_orderby'>{__('Order posts by')}</label>
            <Select
              id='tg_input_orderby'
              onChange={(option) => {
                if (option) setAttributes({ orderby: option.value });
              }}
              value={orderby && typeof orderby === 'string' ? orderby : 'name'}
              options={optionsOrderbyPosts}
            />
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='order'
            />
            <label htmlFor='tg_input_order'>{__('Sort order')}</label>
            <Select
              id='tg_input_order'
              onChange={(option) => {
                if (option) setAttributes({ order: option.value });
              }}
              value={
                order && typeof order === 'string'
                  ? order.toUpperCase()
                  : 'DESC'
              }
              options={optionsOrder}
            />
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='display_amount'
            />
            <ToggleControl
              label={__('Display the total amount of posts.')}
              checked={display_amount}
              onChange={() => toggleOptionDisplayAmount(this)}
            />
          </PanelBody>

          <PanelBody title={__('Post Template')} initialOpen={false}>
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='template'
            />
            <h3>{__('Select a post template')}</h3>
            <ToggleControl
              label={__('Use template from settings')}
              checked={!decodedTemplate}
              onChange={() => setAttributes({ template: '' })}
            />
            <HorizontalRuler color={'#999'} />
            <TagGroupsIcon file={templates[0].image} />
            <ToggleControl
              label={templates[0].label}
              checked={decodedTemplate === templates[0].html}
              onChange={() =>
                setAttributes({
                  template: encodeTemplate(templates[0].html),
                })
              }
            />
            <HorizontalRuler color={'#999'} />
            <TagGroupsIcon file={templates[1].image} />
            <ToggleControl
              label={templates[1].label}
              checked={decodedTemplate === templates[1].html}
              onChange={() =>
                setAttributes({
                  template: encodeTemplate(templates[1].html),
                })
              }
            />
            <HorizontalRuler color={'#999'} />
            <TagGroupsIcon file={templates[2].image} />
            <ToggleControl
              label={templates[2].label}
              checked={decodedTemplate === templates[2].html}
              onChange={() =>
                setAttributes({
                  template: encodeTemplate(templates[2].html),
                })
              }
            />
            <ClearFloats />
            <PanelBody title={__('Customize the template')} initialOpen={false}>
              <PlainText
                id='tg_input_template'
                className='input-control'
                value={decodedTemplate ? decodedTemplate : ''}
                placeholder={__('Write here or leave empty.')}
                onChange={(decodedTemplate) =>
                  setAttributes({
                    template: encodeTemplate(decodedTemplate),
                  })
                }
                className='cm-three-line-input cm-border'
              />
            </PanelBody>
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='default_image_src'
            />
            <label htmlFor='tg_input_default_image_src'>
              {__('Default image src')}
            </label>
            <PlainText
              id='tg_input_default_image_src'
              className='input-control'
              value={default_image_src ? default_image_src : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(default_image_src) =>
                setAttributes({ default_image_src })
              }
            />
          </PanelBody>

          <PanelBody title={__('Labels and Messages')} initialOpen={false}>
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='message_nothing_found'
            />
            <label htmlFor='tg_input_message_nothing_found'>
              {__('Message if nothing was found')}
            </label>
            <PlainText
              id='tg_input_message_nothing_found'
              className='input-control'
              value={message_nothing_found ? message_nothing_found : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(message_nothing_found) =>
                setAttributes({ message_nothing_found })
              }
            />
            {pager == true && (
              <div>
                <TagGroupsHelp
                  url={helpUrl}
                  product={helpProduct}
                  feature={helpFeature}
                  siteLang={siteLang}
                  topic='message_load_more'
                />
                <label htmlFor='tg_input_message_load_more'>
                  {__('Link to load more posts')}
                </label>
                <PlainText
                  id='tg_input_message_load_more'
                  className='input-control'
                  value={message_load_more ? message_load_more : ''}
                  placeholder={__('Write here or leave empty.')}
                  onChange={(message_load_more) =>
                    setAttributes({ message_load_more })
                  }
                />
                <TagGroupsHelp
                  url={helpUrl}
                  product={helpProduct}
                  feature={helpFeature}
                  siteLang={siteLang}
                  topic='message_go_back'
                />
                <label htmlFor='tg_input_message_go_back'>
                  {__('Link to load previous posts')}
                </label>
                <PlainText
                  id='tg_input_message_go_back'
                  className='input-control'
                  value={message_go_back ? message_go_back : ''}
                  placeholder={__('Write here or leave empty.')}
                  onChange={(message_go_back) =>
                    setAttributes({ message_go_back })
                  }
                />
              </div>
            )}
            {display_amount == true && (
              <div>
                <TagGroupsHelp
                  url={helpUrl}
                  product={helpProduct}
                  feature={helpFeature}
                  siteLang={siteLang}
                  topic='message_amount_singular'
                />
                <label htmlFor='tg_input_message_amount_singular'>
                  {__('Message for one post in total')}
                </label>
                <PlainText
                  id='tg_input_message_amount_singular'
                  className='input-control'
                  value={message_amount_singular ? message_amount_singular : ''}
                  placeholder={__('Write here or leave empty.')}
                  onChange={(message_amount_singular) =>
                    setAttributes({ message_amount_singular })
                  }
                />
                <TagGroupsHelp
                  url={helpUrl}
                  product={helpProduct}
                  feature={helpFeature}
                  siteLang={siteLang}
                  topic='message_amount_plural'
                />
                <label htmlFor='tg_input_message_amount_plural'>
                  {__('Message for {count} posts in total')}
                </label>
                <PlainText
                  id='tg_input_message_amount_plural'
                  className='input-control'
                  value={message_amount_plural ? message_amount_plural : ''}
                  placeholder={__('Write here or leave empty.')}
                  onChange={(message_amount_plural) =>
                    setAttributes({ message_amount_plural })
                  }
                />
              </div>
            )}
          </PanelBody>

          <PanelBody title={__('Advanced Styling')} initialOpen={false}>
            <div>
              <TagGroupsHelp
                url={helpUrl}
                product={helpProduct}
                feature={helpFeature}
                siteLang={siteLang}
                topic='div_id'
              />
              <label htmlFor='tg_input_div_id'>{'div id'}</label>
            </div>
            <PlainText
              id='tg_input_div_id'
              className='input-control'
              value={div_id ? div_id : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(div_id) => setAttributes({ div_id })}
            />
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
            <div>
              <TagGroupsHelp
                url={helpUrl}
                product={helpProduct}
                feature={helpFeature}
                siteLang={siteLang}
                topic='article_class'
              />
              <label htmlFor='tg_input_article_class'>{'article class'}</label>
            </div>
            <PlainText
              id='tg_input_article_class'
              className='input-control'
              value={article_class ? article_class : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(article_class) => setAttributes({ article_class })}
            />
          </PanelBody>
        </div>
      </InspectorControls>,
      <div>
        {!!cover && (
          <Fragment>
            <img src={pluginUrl + '/assets/images/features/' + cover} />
          </Fragment>
        )}
        {!cover && serverSideRender && (
          <TagGroupsServerSideRender
            block='chatty-mango/tag-groups-premium-post-filter'
            className='chatty-mango-not-active-all'
            attributes={renderAttributes}
          />
        )}
        {!cover && !serverSideRender && (
          <div className='chatty-mango-editor'>
            <div className='chatty-mango-editor-block'>
            </div>
            <div className='chatty-mango-editor-block'>
              <h3 className='chatty-mango-editor-title'>{__('Post List')}</h3>
              <div className='cm-gutenberg dashicons-before dashicons-admin-generic'>
                {__(
                  'Select this block and customize the filter in the Inspector.'
                )}
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
var cmTagGroupsPostFilterBlock = registerBlockType(
  'chatty-mango/tag-groups-premium-post-filter',
  {
    title: __('Post List'),
    icon: 'list-view', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
    category: 'chatty-mango',
    description: __(
      'Show a static list of posts that corresponds to selected tag groups.'
    ),
    keywords: [__('posts'), __('tags'), 'Chatty Mango'],
    html: false,
    useOnce: false,
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
                  cmTagGroupsPostFilterBlock.attributes[attribute] &&
                  attributes[attribute] !==
                    cmTagGroupsPostFilterBlock.attributes[attribute].default
                ) {
                  if (typeof attributes[attribute] === 'number') {
                    parameters.push(attribute + '=' + attributes[attribute]);
                  } else if (typeof attributes[attribute] === 'boolean') {
                    parameters.push(
                      attribute + '=' + (attributes[attribute] ? '1' : '0')
                    );
                  } else {
                    let str = attributes[attribute];
                    if (str.indexOf('"') === -1) {
                      parameters.push(attribute + '="' + str + '"');
                    } else {
                      parameters.push(attribute + "='" + str + "'");
                    }
                  }
                }
              }
            }

            let text = '[tag_groups_post_list ' + parameters.join(' ') + ']';
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
        cover: 'post-list.png',
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
      article_class: {
        // configurable in block
        type: 'string',
        default: 'tg-post',
      },
      caching_time: {
        // configurable in block
        type: 'integer',
        default: 10,
      },
      default_image_src: {
        // configurable in block
        type: 'string',
        default: '',
      },
      display_amount: {
        // configurable in block
        type: 'integer',
        default: 0,
      },
      div_class: {
        // configurable in block
        type: 'string',
        default: '',
      },
      div_id: {
        // configurable in block
        type: 'string',
        default: '',
      },
      include: {
        // configurable in block
        type: 'string',
        default: '',
      },
      message_amount_plural: {
        // configurable in block
        type: 'string',
        default: __('{count} posts found.'),
      },
      message_amount_singular: {
        // configurable in block
        type: 'string',
        default: __('1 post found.'),
      },
      message_nothing_found: {
        // configurable in block
        type: 'string',
        default: __('Nothing found.'),
      },
      message_load_more: {
        // configurable in block
        type: 'string',
        default: __('Load more'),
      },
      message_go_back: {
        // configurable in block
        type: 'string',
        default: __('Go back'),
      },
      operator: {
        // configurable in block
        type: 'string',
        default: 'IN',
      },
      order: {
        // configurable in block
        type: 'string',
        default: 'DESC',
      },
      orderby: {
        // configurable in block
        type: 'string',
        default: 'date',
      },
      pager: {
        // configurable in block
        type: 'integer',
        default: 0,
      },
      pager_position: {
        // configurable in block
        type: 'string',
        default: 'bottom',
      },
      posts_per_page: {
        // configurable in block
        type: 'integer',
        default: 5,
      },
      taxonomy: {
        // configurable in block
        type: 'string',
        default: '',
      },
      template: {
        // configurable in block
        type: 'string',
        default: '', // Needs to be empty to apply default template from settings
      },
    },

    /**
     * Composing and rendering the editor content and control elements
     */
    edit: editFunction,

    /**
     * We don't render any HTML when saving
     */
    save: function (props) {
      return null;
    },
  }
);
