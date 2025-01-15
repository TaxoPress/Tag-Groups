/**
 * BLOCK: tag-groups-cloud-tabs
 *
 *
 * @package     Tag Groups
 * @author      Christoph Amthor
 * @copyright   2018 Christoph Amthor (@ Chatty Mango, chattymango.com)
 * @license     GPL-3.0+
 * @since       0.38
 */

import '../editor.css';

import Select from 'react-select';

import TagGroupsServerSideRender from '../components/tag-groups-render';

import TagGroupsHelp from '../components/tag-groups-help';

import {
  getIncludeOptions,
  getExcludeOptions,
  getTaxonomyOptions,
  getActiveGroupsOptions,
  optionsOrderby,
  optionsOrder,
  optionsTarget,
  renderTabs,
} from '../modules/functions';

import {
  handleChangeInclude,
  handleChangeExclude,
  handleChangeTaxonomy,
  toggleOptionActive,
  toggleOptionCollapsible,
  toggleOptionMouseover,
  toggleOptionAdjustSeparatorSize,
  toggleOptionAddPremiumFilter,
  toggleOptionHideEmptyTabs,
  toggleOptionShowTabs,
  toggleOptionShowTagCount,
  toggleOptionDelay,
} from '../modules/handlers';

import {
  getGroupsFromApi,
  getTaxonomiesFromApi,
  getPostsFromApi,
} from '../modules/api';

const { __ } = wp.i18n;

const { createBlock, registerBlockType } = wp.blocks;

const { PlainText } = wp.editor;

const { InspectorControls } = wp.blockEditor;

const { PanelBody, ToggleControl, RangeControl } = wp.components;

const { Component, Fragment } = wp.element;

const {
  siteUrl,
  siteLang,
  pluginUrl,
  hasPremium,
  serverSideRender,
  gutenbergSettings,
} = ChattyMangoTagGroupsGlobal;

const helpUrl = '';

class tagGroupsTabbedCloudParameters extends Component {
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
      posts: [],
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
    this.termsEndPoint = '/tag-groups/v1/terms';
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

    if (hasPremium) {
      this.helpProduct = 'tag-groups-premium';
      this.helpFeature = 'tabbed-tag-cloud/tabbed-tag-cloud-gutenberg-block/';
    } else {
      this.helpProduct = 'tag-groups';
      this.helpFeature =
        'tabbed-tag-cloud-tag-clouds-and-groups-info/tabbed-tag-cloud-gutenberg-block-2/';
    }

    // Load data from REST API.
    getGroupsFromApi(this);
    getTaxonomiesFromApi(this);
    getPostsFromApi(this);
  }

  render() {
    const { attributes, setAttributes } = this.props;

    const {
      active,
      add_premium_filter,
      adjust_separator_size,
      amount,
      append,
      assigned_class,
      collapsible,
      cover,
      custom_title,
      custom_title_zero,
      custom_title_plural,
      delay,
      div_class,
      div_id,
      groups_post_id,
      hide_empty_tabs,
      largest,
      link_append,
      link_target,
      mouseover,
      not_assigned_name,
      order,
      orderby,
      prepend,
      separator,
      separator_size,
      show_not_assigned,
      show_tag_count,
      show_tabs,
      smallest,
      tags_post_id,
      threshold,
      ul_class,
    } = attributes;

    if (attributes.source !== 'gutenberg') {
      setAttributes({ source: 'gutenberg' });
    }

    let renderAttributes = { ...attributes };
    renderAttributes.div_id = this.state.uniqueId;
    renderAttributes.delay = 0;
    delete renderAttributes.add_premium_filter;
    delete renderAttributes.cover;
    delete renderAttributes.link_append;
    delete renderAttributes.link_target;
    delete renderAttributes.ul_class;
    delete renderAttributes.custom_title;
    delete renderAttributes.custom_title_zero;
    delete renderAttributes.custom_title_plural;
    delete renderAttributes.assigned_class;

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

    let numberOfTabs = this.state.selectedInclude.length
      ? this.state.selectedInclude.length
      : this.state.groups.length - 1; // unassigned group

    return [
      <InspectorControls key='inspector'>
        <div className='chatty-mango-inspector-control'>
          <PanelBody title={__('Tags and Taxonomies')} initialOpen={false}>
            <TagGroupsHelp
              url={helpUrl}
              product={this.helpProduct}
              feature={this.helpFeature}
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
              product={this.helpProduct}
              feature={this.helpFeature}
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
              product={this.helpProduct}
              feature={this.helpFeature}
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
              product={this.helpProduct}
              feature={this.helpFeature}
              siteLang={siteLang}
              topic='amount'
            />
            <RangeControl
              label={
                __('Tags per group') +
                (amount == 0 ? ': ' + __('unlimited') : '')
              }
              value={amount !== undefined ? Number(amount) : 0}
              onChange={(amount) => setAttributes({ amount })}
              min={0}
              max={200}
            />
            <TagGroupsHelp
              url={helpUrl}
              product={this.helpProduct}
              feature={this.helpFeature}
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
              product={this.helpProduct}
              feature={this.helpFeature}
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
            <div>
              <TagGroupsHelp
                url={helpUrl}
                product={this.helpProduct}
                feature={this.helpFeature}
                siteLang={siteLang}
                topic='separator'
              />
              <label htmlFor='tg_input_separator'>{__('Separator')}</label>
            </div>
            <PlainText
              id='tg_input_separator'
              className='input-control'
              value={separator ? separator : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(separator) => setAttributes({ separator })}
            />
            {separator && (
              <div>
                <TagGroupsHelp
                  url={helpUrl}
                  product={this.helpProduct}
                  feature={this.helpFeature}
                  siteLang={siteLang}
                  topic='adjust_separator_size'
                />
                <ToggleControl
                  label={__('Adjust separator size to following tag')}
                  checked={adjust_separator_size}
                  onChange={() => toggleOptionAdjustSeparatorSize(this)}
                />
                {!adjust_separator_size && (
                  <div>
                    <TagGroupsHelp
                      url={helpUrl}
                      product={this.helpProduct}
                      feature={this.helpFeature}
                      siteLang={siteLang}
                      topic='separator_size'
                    />
                    <RangeControl
                      label={__('Separator size')}
                      value={
                        separator_size !== undefined
                          ? Number(separator_size)
                          : 22
                      }
                      onChange={(separator_size) =>
                        setAttributes({ separator_size })
                      }
                      min={6}
                      max={144}
                    />
                  </div>
                )}
              </div>
            )}
            <TagGroupsHelp
              url={helpUrl}
              product={this.helpProduct}
              feature={this.helpFeature}
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
              product={this.helpProduct}
              feature={this.helpFeature}
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
                  product={this.helpProduct}
                  feature={this.helpFeature}
                  siteLang={siteLang}
                  topic='show_tag_count'
                />
                <ToggleControl
                  label={__('Show post count in the tooltip')}
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
              product={this.helpProduct}
              feature={this.helpFeature}
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
                {__('Append to link')}
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
                  product={this.helpProduct}
                  feature={this.helpFeature}
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
              product={this.helpProduct}
              feature={this.helpFeature}
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

          <PanelBody title={__('Groups and Tabs')} initialOpen={false}>
            <TagGroupsHelp
              url={helpUrl}
              product={this.helpProduct}
              feature={this.helpFeature}
              siteLang={siteLang}
              topic='delay'
            />
            <ToggleControl
              label={__(
                'Delay the display of the tabs until they are fully rendered'
              )}
              checked={delay}
              onChange={() => toggleOptionDelay(this)}
            />
            <TagGroupsHelp
              url={helpUrl}
              product={this.helpProduct}
              feature={this.helpFeature}
              siteLang={siteLang}
              topic='show_tabs'
            />
            <ToggleControl
              label={__('Show tabs')}
              checked={show_tabs}
              onChange={() => toggleOptionShowTabs(this)}
            />
            <TagGroupsHelp
              url={helpUrl}
              product={this.helpProduct}
              feature={this.helpFeature}
              siteLang={siteLang}
              topic='hide_empty_tabs'
            />
            <ToggleControl
              label={__('Hide empty tabs')}
              checked={hide_empty_tabs}
              onChange={() => toggleOptionHideEmptyTabs(this)}
            />
            <TagGroupsHelp
              url={helpUrl}
              product={this.helpProduct}
              feature={this.helpFeature}
              siteLang={siteLang}
              topic='mouseover'
            />
            <ToggleControl
              label={__('Open tabs on mouseover')}
              checked={mouseover}
              onChange={() => toggleOptionMouseover(this)}
            />
            <TagGroupsHelp
              url={helpUrl}
              product={this.helpProduct}
              feature={this.helpFeature}
              siteLang={siteLang}
              topic='collapsible'
            />
            <ToggleControl
              label={__('Make panels collapsible')}
              checked={collapsible}
              onChange={() => toggleOptionCollapsible(this)}
            />
            {!!collapsible && numberOfTabs && (
              <div>
                <TagGroupsHelp
                  url={helpUrl}
                  product={this.helpProduct}
                  feature={this.helpFeature}
                  siteLang={siteLang}
                  topic='active'
                />
                <ToggleControl
                  label={__('Start with expanded tabs')}
                  checked={active >= 0}
                  onChange={() => toggleOptionActive(this)}
                />
              </div>
            )}
            {(active >= 0 || !collapsible) && (
              <div>
                <TagGroupsHelp
                  url={helpUrl}
                  product={this.helpProduct}
                  feature={this.helpFeature}
                  siteLang={siteLang}
                  topic='select_active'
                />
                <label htmlFor='tg_input_active'>
                  {__('Which tab should be initially open?')}
                </label>
                <Select
                  id='tg_input_active'
                  onChange={(option) => {
                    if (option) setAttributes({ active: option.value });
                  }}
                  value={active < 0 ? 0 : active}
                  options={getActiveGroupsOptions(this)}
                />
              </div>
            )}
            <TagGroupsHelp
              url={helpUrl}
              product={this.helpProduct}
              feature={this.helpFeature}
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
            {show_not_assigned === 1 && (
              <div>
                <div>
                  <label htmlFor='tg_input_not_assigned_name'>
                    {__('Label on tab for not-assigned tags')}
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
              product={this.helpProduct}
              feature={this.helpFeature}
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

          <PanelBody title={__('Advanced Styling')} initialOpen={false}>
            <div>
              <TagGroupsHelp
                url={helpUrl}
                product={this.helpProduct}
                feature={this.helpFeature}
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
                product={this.helpProduct}
                feature={this.helpFeature}
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
                product={this.helpProduct}
                feature={this.helpFeature}
                siteLang={siteLang}
                topic='ul_class'
              />
              <label htmlFor='tg_input_ul_class'>{'ul class'}</label>
            </div>
            <PlainText
              id='tg_input_ul_class'
              className='input-control'
              value={ul_class ? ul_class : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(ul_class) => setAttributes({ ul_class })}
            />
            {tags_post_id !== -1 && (
              <div>
                <div>
                  <TagGroupsHelp
                    url={helpUrl}
                    product={this.helpProduct}
                    feature={this.helpFeature}
                    siteLang={siteLang}
                    topic='assigned_class'
                  />
                  <label htmlFor='tg_input_assigned_class'>
                    {'<a class="..._0"> or <a class="..._1">'}
                  </label>
                </div>
                <PlainText
                  id='tg_input_assigned_class'
                  className='input-control'
                  value={assigned_class ? assigned_class : ''}
                  placeholder={__('Write here or leave empty.')}
                  onChange={(assigned_class) =>
                    setAttributes({ assigned_class })
                  }
                />
              </div>
            )}
          </PanelBody>
          <div className='chatty-mango-help-transform'>
            <TagGroupsHelp
              url={helpUrl}
              product={this.helpProduct}
              feature={this.helpFeature}
              siteLang={siteLang}
              topic='transform-your-block-for-more-options'
            />
            <div
              className='dashicons-before dashicons-editor-code'
              dangerouslySetInnerHTML={{
                __html: __(
                  'If you want to customize further options, you need to transform the block into a <b>shortcode block</b>.'
                ),
              }}
            ></div>
          </div>
          <div
            className='chatty-mango-inspector-help dashicons-before dashicons-admin-generic'
            dangerouslySetInnerHTML={{
              __html: __(
                `The live preview of blocks can be turned on and off in the Tag Groups Settings under <a href="${gutenbergSettings}">Back End → Gutenberg</a>.`,
                'tag-groups'
              ),
            }}
          ></div>
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
            block='chatty-mango/tag-groups-cloud-tabs'
            className='chatty-mango-not-active'
            attributes={renderAttributes}
            onFetched={() => renderTabs(this)}
          />
        )}
        {!cover && !serverSideRender && (
          <div className='chatty-mango-editor'>
            <div className='chatty-mango-editor-block'>
            </div>
            <div className='chatty-mango-editor-block'>
              <h3 className='chatty-mango-editor-title'>
                {__('Tabbed Tag Cloud')}
              </h3>
              <div className='cm-gutenberg dashicons-before dashicons-admin-generic'>
                {__(
                  'Select this block and customize the tag cloud in the Inspector.'
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
var cmTagGroupsTabsBlock = registerBlockType(
  'chatty-mango/tag-groups-cloud-tabs',
  {
    title: __('Tabbed Tag Cloud'),
    icon: 'tagcloud', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
    category: 'chatty-mango',
    description: __('Show your tags in groups under tabs.'),
    keywords: [__('tabs'), __('tag cloud'), 'Chatty Mango'],
    html: false,
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
                  cmTagGroupsTabsBlock.attributes[attribute] &&
                  attributes[attribute] !==
                    cmTagGroupsTabsBlock.attributes[attribute].default
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

            let text = '[tag_groups_cloud ' + parameters.join(' ') + ']';
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
        cover: 'tabbed-tag-cloud.png',
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
      active: {
        // configurable in block
        type: 'integer',
        default: -1,
      },
      adjust_separator_size: {
        // configurable in block
        type: 'integer',
        default: 1,
      },
      add_premium_filter: {
        // configurable in block
        type: 'integer',
        default: 0,
      },
      amount: {
        // configurable in block
        type: 'integer',
        default: 0,
      },
      append: {
        // configurable in block
        type: 'string',
        default: '',
      },
      assigned_class: {
        // configurable in block
        type: 'string',
        default: '',
      },
      collapsible: {
        // configurable in block
        type: 'integer',
        default: 0,
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
      delay: {
        // configurable in block
        type: 'integer',
        default: 1,
      },
      div_class: {
        // configurable in block
        type: 'string',
        default: 'tag-groups-cloud',
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
      exclude_terms: {
        // only in shortcode
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
      hide_empty_tabs: {
        // configurable in block
        type: 'integer',
        default: 0,
      },
      include: {
        // configurable in block
        type: 'string',
        default: '',
      },
      include_terms: {
        // only in shortcode
        type: 'string',
        default: '',
      },
      largest: {
        // configurable in block
        type: 'integer',
        default: 22,
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
      mouseover: {
        // configurable in block
        type: 'integer',
        default: 0,
      },
      not_assigned_name: {
        // configurable in block
        type: 'string',
        default: 'not assigned',
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
      prepend: {
        // configurable in block
        type: 'string',
        default: '',
      },
      separator_size: {
        // configurable in block
        type: 'integer',
        default: 22,
      },
      separator: {
        // configurable in block
        type: 'string',
        default: '',
      },
      show_not_assigned: {
        // indirectly configurable in block
        type: 'integer',
        default: 0,
      },
      show_all_groups: {
        // only in shortcode
        type: 'integer',
        default: 0,
      },
      show_tabs: {
        // configurable in block
        type: 'integer',
        default: 1,
      },
      show_tag_count: {
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
      ul_class: {
        // configurable in block
        type: 'string',
        default: '',
      },
    },

    /**
     * Composing and rendering the editor content and control elements
     */
    edit: tagGroupsTabbedCloudParameters,

    /**
     * We don't render any HTML when saving
     */
    save: function (props) {
      return null;
    },
  }
);
