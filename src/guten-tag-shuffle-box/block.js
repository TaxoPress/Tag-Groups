/**
 * BLOCK: tag-groups-premium-shuffle-box
 *
 *
 * @package     Tag Groups
 * @author      Christoph Amthor
 * @copyright   2018 Christoph Amthor (@ Chatty Mango, chattymango.com)
 */

import '../editor.css';

import Select from 'react-select';

import TagGroupsServerSideRender from '../components/tag-groups-render';

import TagGroupsHelp from '../components/tag-groups-help';

import {
  getIncludeOptions,
  getExcludeOptions,
  getTaxonomyOptions,
  getInitialGroupOptions,
  optionsOrderby,
  optionsOrder,
  optionsTarget,
  optionsLayoutMode,
  renderIsotope,
} from '../modules/functions';

import {
  handleChangeInclude,
  handleChangeExclude,
  handleChangeTaxonomy,
  toggleOptionAddPremiumFilter,
  toggleOptionShowTagCount,
  toggleOptionShowGroupFilter,
  toggleOptionShowTextFilter,
  toggleOptionShowFilterAllGroups,
} from '../modules/handlers';

import {
  getGroupsFromApi,
  getTaxonomiesFromApi,
  getPostsFromApi,
} from '../modules/api';

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
  serverSideRender,
} = ChattyMangoTagGroupsGlobal;

const helpUrl = '';
const helpProduct = hasPremium ? 'tag-groups-premium' : 'tag-groups';
const helpFeature = 'shuffle-box/shuffle-box-gutenberg-block/';

class tagGroupsPremiumShuffleBoxParameters extends Component {
  // Method for setting the initial state.
  static getInitialState(attributes) {
    let selectedInclude = []; // empty means all
    let selectedExclude = []; // empty means none
    let selectedTaxonomies = ['post_tag'];
    let uniqueId =
      'tag_groups_render_' + Math.random().toString(36).substring(7);

    // We need arrays for the select elements.
    if (attributes.include) {
      selectedInclude = attributes.include.split(',').map((x) => {
        return parseInt(x, 10);
      });
    }
    if (attributes.exclude) {
      selectedExclude = attributes.exclude.split(',').map((x) => {
        return parseInt(x, 10);
      });
    }

    if (attributes.taxonomy) {
      selectedTaxonomies = attributes.taxonomy.split(',');
    }

    return {
      groups: [],
      taxonomies: [],
      selectedInclude, // array representation
      selectedExclude,
      selectedTaxonomies, // array representation
      uniqueId,
    };
  }

  // Constructing our component. With super() we are setting everything to 'this'.
  // Now we can access the attributes with this.props.attributes
  constructor() {
    super(...arguments);

    const { attributes, setAttributes } = this.props;

    this.groupsEndPoint = '/tag-groups/v1/groups';
    // this.termsEndPoint = '/tag-groups/v1/terms';
    this.taxonomiesEndPoint = '/tag-groups/v1/taxonomies';

    this.state = this.constructor.getInitialState(attributes);

    if (!attributes.hide_empty) {
      setAttributes({ threshold: 0 });
    }

    if (attributes.threshold) {
      setAttributes({ hide_empty: 1 });
    } else {
      setAttributes({ hide_empty: 0 });
    }

    this.helpProduct = helpProduct;

    // Load data from REST API.
    getGroupsFromApi(this);
    getTaxonomiesFromApi(this);
    getPostsFromApi(this);
  }

  render() {
    const { attributes, setAttributes } = this.props;

    const {
      add_premium_filter,
      amount,
      append,
      cover,
      custom_title,
      custom_title_zero,
      custom_title_plural,
      div_class,
      div_id,
      groups_post_id,
      initial_group,
      largest,
      layout_mode,
      link_append,
      link_target,
      not_assigned_name,
      order,
      orderby,
      placeholder,
      prepend,
      show_all_name,
      show_filter_all_groups,
      show_group_filter,
      show_tag_count,
      show_text_filter,
      smallest,
      tags_post_id,
      threshold,
    } = attributes;

    if (attributes.source !== 'gutenberg') {
      setAttributes({ source: 'gutenberg' });
    }

    let renderAttributes = { ...attributes };
    renderAttributes.div_id = this.state.uniqueId;
    delete renderAttributes.add_premium_filter;
    delete renderAttributes.cover;
    delete renderAttributes.link_append;
    delete renderAttributes.link_target;
    delete renderAttributes.custom_title;
    delete renderAttributes.custom_title_zero;
    delete renderAttributes.custom_title_plural;
    renderAttributes.source = 'serverSideRender';

    if (0 === tags_post_id) {
      renderAttributes.tags_post_id = wp.data
        .select('core/editor')
        .getCurrentPostId();
    }

    if (0 === groups_post_id) {
      renderAttributes.groups_post_id = wp.data
        .select('core/editor')
        .getCurrentPostId();
    }

    return [
      <InspectorControls key='inspector'>
        <div className='chatty-mango-inspector-control'>
          <PanelBody title={__('Tags and Taxonomies')} initialOpen={false}>
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='layout_mode'
            />
            <label htmlFor='tg_input_layout_mode'>{__('Layout Mode')}</label>
            <Select
              id='tg_input_layout_mode'
              onChange={(option) => {
                if (option) setAttributes({ layout_mode: option.value });
              }}
              value={
                layout_mode && typeof layout_mode === 'string'
                  ? layout_mode
                  : 'fitRows'
              }
              options={optionsLayoutMode}
            />
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
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='smallest'
            />
            <RangeControl
              label={__('Smallest font size')}
              value={smallest !== undefined ? Number(smallest) : 12}
              onChange={(smallest) => {
                if (smallest <= largest && smallest < 73)
                  setAttributes({ smallest });
              }}
              min={6}
              max={72}
            />
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='largest'
            />
            <RangeControl
              label={__('Largest font size')}
              value={largest !== undefined ? Number(largest) : 22}
              onChange={(largest) => {
                if (smallest <= largest && largest > 5)
                  setAttributes({ largest });
              }}
              min={6}
              max={72}
            />
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='amount'
            />
            <RangeControl
              label={
                __('Total amount of tags') +
                (amount == 0 ? ': ' + __('unlimited') : '')
              }
              value={amount !== undefined ? Number(amount) : 0}
              onChange={(amount) => setAttributes({ amount })}
              min={0}
              max={200}
            />
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='orderby'
            />
            <label htmlFor='tg_input_orderby'>{__('Order tags by')}</label>
            <Select
              id='tg_input_orderby'
              onChange={(option) => {
                if (option) setAttributes({ orderby: option.value });
              }}
              value={orderby && typeof orderby === 'string' ? orderby : 'name'}
              options={optionsOrderby}
            />
            {orderby !== 'random' && (
              <div>
                <TagGroupsHelp
                  url={helpUrl}
                  product={this.helpProduct}
                  feature={this.helpFeature}
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
                      : 'ASC'
                  }
                  options={optionsOrder}
                />
              </div>
            )}
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='threshold'
            />
            <RangeControl
              label={__('Minimum post count for tags to appear')}
              value={threshold !== undefined ? Number(threshold) : 0}
              onChange={(threshold) => {
                setAttributes({ threshold });
                if (0 === threshold) {
                  setAttributes({ hide_empty: 0 });
                } else {
                  setAttributes({ hide_empty: 1 });
                }
              }}
              min={0}
              max={50}
            />
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='prepend'
            />
            <div>
              <label htmlFor='tg_input_prepend'>{__('Prepend')}</label>
            </div>
            <PlainText
              id='tg_input_prepend'
              className='input-control'
              value={prepend ? prepend : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(prepend) => setAttributes({ prepend })}
            />
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='append'
            />
            <div>
              <label htmlFor='tg_input_append'>{__('Append')}</label>
            </div>
            <PlainText
              id='tg_input_append'
              className='input-control'
              value={append ? append : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(append) => setAttributes({ append })}
            />
            {!custom_title && (
              <div>
                <TagGroupsHelp
                  url={helpUrl}
                  product={helpProduct}
                  feature={helpFeature}
                  siteLang={siteLang}
                  topic='show_tag_count'
                />
                <ToggleControl
                  label={__('Show the post count in the tooltip')}
                  checked={show_tag_count}
                  onChange={() => toggleOptionShowTagCount(this)}
                />
              </div>
            )}
            {attributes.hide_empty === 0 && threshold < 1 ? (
              <div>
                <div>
                  <TagGroupsHelp
                    url={helpUrl}
                    product={this.helpProduct}
                    feature={this.helpFeature}
                    siteLang={siteLang}
                    topic='custom_title_zero'
                  />
                  <label htmlFor='tg_input_custom_title_zero'>
                    {__('Custom title for post count 0')}
                  </label>
                </div>
                <PlainText
                  id='tg_input_custom_title_zero'
                  className='input-control'
                  value={custom_title_zero ? custom_title_zero : ''}
                  placeholder={__('Write here or leave empty.')}
                  onChange={(custom_title_zero) =>
                    setAttributes({ custom_title_zero })
                  }
                />
              </div>
            ) : (
              ''
            )}
            <div>
              <TagGroupsHelp
                url={helpUrl}
                product={this.helpProduct}
                feature={this.helpFeature}
                siteLang={siteLang}
                topic='custom_title'
              />
              <label htmlFor='tg_input_custom_title'>
                {__('Custom title for post count 1')}
              </label>
            </div>
            <PlainText
              id='tg_input_custom_title'
              className='input-control'
              value={custom_title ? custom_title : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(custom_title) => setAttributes({ custom_title })}
            />
            <div>
              <TagGroupsHelp
                url={helpUrl}
                product={this.helpProduct}
                feature={this.helpFeature}
                siteLang={siteLang}
                topic='custom_title_plural'
              />
              <label htmlFor='tg_input_custom_title_plural'>
                {__('Custom title for post count > 1')}
              </label>
            </div>
            <PlainText
              id='tg_input_custom_title_plural'
              className='input-control'
              value={custom_title_plural ? custom_title_plural : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(custom_title_plural) => setAttributes({ custom_title_plural })}
            />
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='link_target'
            />
            <label htmlFor='tg_input_link_target'>{__('Link target')}</label>
            <Select
              id='tg_input_link_target'
              onChange={(option) => {
                if (option) setAttributes({ link_target: option.value });
              }}
              value={
                link_target && typeof link_target === 'string'
                  ? link_target
                  : '_self'
              }
              options={optionsTarget}
            />
            <div>
              <label htmlFor='tg_input_link_append'>
                {__('Append to the link')}
              </label>
            </div>
            <PlainText
              id='tg_input_link_append'
              className='input-control'
              value={link_append ? link_append : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(link_append) => setAttributes({ link_append })}
            />
            {hasPremium && (
              <div>
                <TagGroupsHelp
                  url={helpUrl}
                  product={helpProduct}
                  feature={helpFeature}
                  siteLang={siteLang}
                  topic='add_premium_filter'
                />
                <ToggleControl
                  label={__('Add filter to tags for multiple groups.')}
                  checked={add_premium_filter}
                  onChange={() => toggleOptionAddPremiumFilter(this)}
                />
              </div>
            )}
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='tags_post_id'
            />
            <label htmlFor='tg_input_tags_post_id'>
              {__('Use tags of the following post:')}
            </label>
            <Select
              id='tg_input_tags_post_id'
              onChange={(option) => {
                if (option && option.value > -2)
                  setAttributes({ tags_post_id: option.value });
              }}
              value={tags_post_id}
              options={this.state.posts}
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
              product={this.helpProduct}
              feature={this.helpFeature}
              siteLang={siteLang}
              topic='exclude'
            />
            <label htmlFor='tg_input_exclude'>{__('Exclude groups')}</label>
            <Select
              id='tg_input_exclude'
              onChange={(options) => handleChangeExclude(this, options)}
              value={this.state.selectedExclude}
              options={getExcludeOptions(this)}
              multi={true}
              closeOnSelect={false}
              removeSelected={true}
            />
            {this.state.selectedInclude.indexOf(-1) > -1 && (
              <div>
                <div>
                  <label htmlFor='tg_input_not_assigned_name'>
                    {__('Label on button for not assigned tags')}
                  </label>
                </div>
                <PlainText
                  id='tg_input_not_assigned_name'
                  className='input-control'
                  value={not_assigned_name ? not_assigned_name : 'not assigned'}
                  placeholder={__('Write here or leave empty.')}
                  onChange={(not_assigned_name) =>
                    setAttributes({ not_assigned_name })
                  }
                />
              </div>
            )}
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='groups_post_id'
            />
            <label htmlFor='tg_input_group_post_id'>
              {__('Use groups of the following post:')}
            </label>
            <Select
              id='tg_input_group_post_id'
              onChange={(option) => {
                if (option && option.value > -2)
                  setAttributes({ groups_post_id: option.value });
              }}
              value={groups_post_id}
              options={this.state.posts}
            />
          </PanelBody>

          <PanelBody title={__('Filters')} initialOpen={false}>
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='show_text_filter'
            />
            <ToggleControl
              label={__('Show input field to filter by text.')}
              checked={show_text_filter}
              onChange={() => toggleOptionShowTextFilter(this)}
            />
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='show_group_filter'
            />
            <ToggleControl
              label={__('Show buttons to filter by group.')}
              checked={show_group_filter}
              onChange={() => toggleOptionShowGroupFilter(this)}
            />
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='show_filter_all_groups'
            />
            {!!show_group_filter && (
              <Fragment>
                <ToggleControl
                  label={__('Show Filter "All Groups"')}
                  checked={show_filter_all_groups}
                  onChange={() => toggleOptionShowFilterAllGroups(this)}
                />
                <TagGroupsHelp
                  url={helpUrl}
                  product={helpProduct}
                  feature={helpFeature}
                  siteLang={siteLang}
                  topic='initial_group'
                />
                <label htmlFor='tg_input_initial_group'>
                  {__('Initially selected filter')}
                </label>
                <Select
                  id='tg_input_initial_group'
                  onChange={(option) => {
                    if (option) setAttributes({ initial_group: option.value });
                  }}
                  value={initial_group ? initial_group : -1}
                  options={getInitialGroupOptions(this)}
                  multi={false}
                />
              </Fragment>
            )}
          </PanelBody>

          {show_group_filter === 1 || show_text_filter === 1 ? (
            <PanelBody title={__('Labels and Messages')} initialOpen={false}>
              {show_text_filter ? (
                <div>
                  <TagGroupsHelp
                    url={helpUrl}
                    product={helpProduct}
                    feature={helpFeature}
                    siteLang={siteLang}
                    topic='placeholder'
                  />
                  <label htmlFor='tg_input_placeholder'>
                    {__('Placeholder for text field')}
                  </label>
                  <PlainText
                    id='tg_input_placeholder'
                    className='input-control'
                    value={placeholder ? placeholder : ''}
                    placeholder={__('Write here or leave empty.')}
                    onChange={(placeholder) => setAttributes({ placeholder })}
                  />
                </div>
              ) : (
                ''
              )}
              {show_group_filter ? (
                <div>
                  {this.state.selectedInclude &&
                    this.state.selectedInclude.indexOf(0) > -1 && (
                      <div>
                        <TagGroupsHelp
                          url={helpUrl}
                          product={helpProduct}
                          feature={helpFeature}
                          siteLang={siteLang}
                          topic='not_assigned_name'
                        />
                        <label htmlFor='tg_input_not_assigned_name'>
                          {__('Label for "not assigned" button')}
                        </label>
                        <PlainText
                          id='tg_input_not_assigned_name'
                          className='input-control'
                          value={not_assigned_name ? not_assigned_name : ''}
                          placeholder={__('Write here or leave empty.')}
                          onChange={(not_assigned_name) =>
                            setAttributes({ not_assigned_name })
                          }
                        />
                      </div>
                    )}
                  <TagGroupsHelp
                    url={helpUrl}
                    product={helpProduct}
                    feature={helpFeature}
                    siteLang={siteLang}
                    topic='show_all_name'
                  />
                  <label htmlFor='tg_input_show_all_name'>
                    {__('Label for "show all" button')}
                  </label>
                  <PlainText
                    id='tg_input_show_all_name'
                    className='input-control'
                    value={show_all_name ? show_all_name : ''}
                    placeholder={__('Write here or leave empty.')}
                    onChange={(show_all_name) =>
                      setAttributes({ show_all_name })
                    }
                  />
                </div>
              ) : (
                ''
              )}
            </PanelBody>
          ) : (
            ''
          )}

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
              <label htmlFor='tg_input_div_class'>{__('div class')}</label>
            </div>
            <PlainText
              id='tg_input_div_class'
              className='input-control'
              value={div_class ? div_class : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(div_class) => setAttributes({ div_class })}
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
            block='chatty-mango/tag-groups-premium-shuffle-box'
            className='chatty-mango-not-active-all'
            attributes={renderAttributes}
            onFetched={() => renderIsotope(this)}
          />
        )}
        {!cover && !serverSideRender && (
          <div className='chatty-mango-editor'>
            <div className='chatty-mango-editor-block'>
            </div>
            <div className='chatty-mango-editor-block'>
              <h3 className='chatty-mango-editor-title'>{__('Shuffle Box')}</h3>
              <div className='cm-gutenberg dashicons-before dashicons-admin-generic'>
                {__(
                  'Select this block and customize the Shuffle Box in the Inspector.'
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
var cmTagGroupsTableBlock = registerBlockType(
  'chatty-mango/tag-groups-premium-shuffle-box',
  {
    title: __('Shuffle Box'),
    icon: 'tagcloud', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
    category: 'chatty-mango',
    description: __('Show your tags in a searchable tag cloud.'),
    keywords: [__('shuffle'), __('tag cloud'), 'Chatty Mango'],
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
                  cmTagGroupsTableBlock.attributes[attribute] &&
                  attributes[attribute] !==
                    cmTagGroupsTableBlock.attributes[attribute].default
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

            let text = '[tag_groups_shuffle_box ' + parameters.join(' ') + ']';
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
        cover: 'shuffle-box.png',
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
      add_premium_filter: {
        // configurable in block
        type: 'integer',
        default: 0,
      },
      amount: {
        // configurable in block
        type: 'integer',
        default: 200,
      },
      append: {
        // configurable in block
        type: 'string',
        default: '',
      },
      custom_title: {
        // configurable in block
        type: 'string',
        default: '{description} ({count})',
      },
      custom_title_zero: {
        // configurable in block
        type: 'string',
        default: '{description} ({count})',
      },
      custom_title_plural: {
        // configurable in block
        type: 'string',
        default: '{description} ({count})',
      },
      div_class: {
        // configurable in block
        type: 'string',
        default: 'cm-shuffle-box-theme-default',
      },
      div_id: {
        // configurable in block
        type: 'string',
        default: '',
      },
      exclude: {
        // configurable in block
        type: 'string',
        default: '',
      },
      groups_post_id: {
        // configurable in block
        type: 'integer',
        default: -1,
      },
      hide_empty: {
        // configurable in block
        type: 'integer',
        default: 1,
      },
      include: {
        // configurable in block
        type: 'string',
        default: '',
      },
      initial_group: {
        // configurable in block
        type: 'integer',
        default: -1,
      },
      largest: {
        // configurable in block
        type: 'integer',
        default: 22,
      },
      layout_mode: {
        // configurable in block
        type: 'string',
        default: 'fitRows',
      },
      link_append: {
        // configurable in block
        type: 'string',
        default: '',
      },
      link_target: {
        // configurable in block
        type: 'string',
        default: '_self',
      },
      not_assigned_name: {
        // configurable in block
        type: 'string',
        default: __('not assigned'),
      },
      order: {
        // configurable in block
        type: 'string',
        default: 'ASC',
      },
      orderby: {
        // configurable in block
        type: 'string',
        default: 'name',
      },
      placeholder: {
        // configurable in block
        type: 'string',
        default: __('search'),
      },
      prepend: {
        // configurable in block
        type: 'string',
        default: '',
      },
      show_all_name: {
        // configurable in block
        type: 'string',
        default: __('all groups'),
      },
      show_filter_all_groups: {
        // configurable in block
        type: 'integer',
        default: 1,
      },
      show_group_filter: {
        // configurable in block
        type: 'integer',
        default: 1,
      },
      show_tag_count: {
        // configurable in block
        type: 'integer',
        default: 1,
      },
      show_text_filter: {
        // configurable in block
        type: 'integer',
        default: 1,
      },
      smallest: {
        // configurable in block
        type: 'integer',
        default: 12,
      },
      tags_post_id: {
        // configurable in block
        type: 'integer',
        default: -1,
      },
      taxonomy: {
        // configurable in block
        type: 'string',
        default: '',
      },
      threshold: {
        // configurable in block
        type: 'integer',
        default: 0,
      },
    },

    /**
     * Composing and rendering the editor content and control elements
     */
    edit: tagGroupsPremiumShuffleBoxParameters,

    /**
     * We don't render any HTML when saving
     */
    save: function (props) {
      return null;
    },
  }
);
