/**
 * BLOCK: chatty-mango-tpf-menu
 *
 *
 * @package     Tag Groups Pro
 * @author      Christoph Amthor
 * @copyright   2018 Christoph Amthor (@ Chatty Mango, chattymango.com)
 */

//	Import CSS.
// import './style.scss';
import '../editor.css';

import Select from 'react-select';
import apiFetch from '@wordpress/api-fetch';
import TagGroupsServerSideRender from '../components/tag-groups-render';
import {
  Dashicon,
  Button,
  ButtonGroup,
  ColorPicker,
} from '@wordpress/components';

import TagGroupsHelp from '../components/tag-groups-help';

import {
  getExcludeOptions,
  getIncludeOptions,
  getTaxonomyOptions,
  optionsAccordion,
  optionsOrder,
  optionsOrderby,
  optionsTextSearch,
  optionsTheme,
} from '../modules/functions';

import {
  getGroupsFromApi,
  getTaxonomiesFromApi,
  getAllTaxonomiesFromApi,
  getAllTermsFromApi,
} from '../modules/api';

import {
  handleChangeExclude,
  handleChangeInclude,
  handleChangeOneOnlyGroups,
  handleChangeTaxonomy,
  handleChangeStaticTaxonomy,
  handleChangeStaticTerms,
  handleChangeIncludeTerms,
  handleChangeExcludeTerms,
  handleChangePresetTags,
  toggleOptionHideEmpty,
} from '../modules/handlers';

const { __ } = wp.i18n;

const { createBlock, getBlockType, registerBlockType } = wp.blocks;

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
const helpFeature = 'toggle-post-filter/toggle-post-filter-gutenberg-block/';
const legacyTPFMenuBlockName = 'chatty-mango/chatty-mango-tpf-menu';
const defaultTPFMenuBlockTitle = __('Toggle Post Filter - Menu');

class editFunction extends Component {
  // Method for setting the initial state.
  static getInitialState(attributes) {
    let selectedExclude = []; // empty means none
    let selectedInclude = []; // empty means all
    let selectedOneOnlyGroups = []; // empty means none
    let selectedTaxonomies = ['post_tag'];
    let selectedStaticTerms = [];
    let selectedIncludeTerms = [];
    let selectedExcludeTerms = [];
    let selectedPresetTags = [];

    const {
      exclude,
      exclude_terms,
      include,
      include_terms,
      one_only_groups,
      preset_tags,
      static_terms,
      taxonomy,
    } = attributes;

    // We need arrays for the select elements.
    if (exclude) {
      selectedExclude = exclude.split(',').map((x) => {
        return parseInt(x, 10);
      });
    }

    if (include) {
      selectedInclude = include.split(',').map((x) => {
        return parseInt(x, 10);
      });
    }

    if (one_only_groups) {
      selectedOneOnlyGroups = one_only_groups.split(',').map((x) => {
        return parseInt(x, 10);
      });
    }

    const text_search = attributes.text_search || 0;

    const selectedStaticTaxonomy = attributes.static_taxonomy || '';

    const layout = attributes.layout || 'classic';

    const theme = attributes.theme || 'light';

    if (taxonomy) {
      selectedTaxonomies = taxonomy.split(',');
    }

    if (static_terms) {
      selectedStaticTerms = static_terms.split(',');
    }

    if (include_terms) {
      selectedIncludeTerms = include_terms.split(',');
    }

    if (exclude_terms) {
      selectedExcludeTerms = exclude_terms.split(',');
    }

    if (preset_tags) {
      selectedPresetTags = preset_tags.split(',');
    }

    return {
      allTerms: [],
      allTaxonomies: {},
      taxonomies: [],
      groups: [],
      selectedStaticTaxonomy,
      selectedStaticTerms,
      selectedIncludeTerms,
      selectedExcludeTerms,
      selectedTaxonomies,
      selectedExclude,
      selectedInclude,
      selectedOneOnlyGroups,
      layout,
      theme,
      text_search,
      selectedPresetTags,
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
    this.allTaxonomiesEndPoint = '/wp/v2/taxonomies';

    this.state = this.constructor.getInitialState(this.props.attributes);

    attributes.operator &&
      setAttributes({
        operator: attributes.operator.toUpperCase(),
      });

    // Load data from REST API
    getGroupsFromApi(this);
    getAllTaxonomiesFromApi(this);
    getAllTermsFromApi(this);
    getTaxonomiesFromApi(this);

    if (
      typeof tagGroupsToggleFilterMenuLegacy !== 'undefined' &&
      !attributes.cover
    ) {
      tagGroupsToggleFilterMenuLegacy = false;
    }

    if (typeof tagGroupsTPFMenuLoaded !== 'undefined' && !attributes.cover) {
      tagGroupsTPFMenuLoaded = true;
    }
  }

  finalizeLayout = () => {
    const { accordion } = this.props.attributes;

    setTimeout(() => {
      const textSearch = document.getElementById('tg_dpf_toggle_text_search');
      if (textSearch) {
        textSearch.classList.add('chatty-mango-not-active-all');
      }

      if (accordion) {
        let options = {
          collapsible: true,
          heightStyle: 'content',
          active: false,
          animate: 800,
        };
        if (accordion > 1) {
          options['event'] = 'mouseover';
        }
        jQuery('.tg_group_dpf_toggle_group_container').accordion(options, 1000);
      }
    });
  };

  render() {
    const { attributes, setAttributes } = this.props;

    const {
      accordion,
      caching_time,
      cover,
      div_class,
      div_id,
      hide_empty,
      icon_class,
      layout,
      operator,
      persistent_filter,
      placeholder_text_search,
      selected_tag_color,
      slider_width,
      source,
      tag_color,
      term_order,
      term_orderby,
      text_search,
      timeout,
      title_text_search,
      theme,
    } = attributes;

    let optionsStaticTaxonomy = [{ value: '', label: 'none' }],
      optionsStaticTerms = [{ value: '', label: 'none' }],
      optionsOneOnlyGroups = [],
      optionsIncludeTerms = [],
      optionsExcludeTerms = [],
      optionsPresetTags = [];

    const optionsLayout = [
      { value: 'classic', label: 'classic (vertical)' },
      { value: 'classic_tags', label: 'classic with tags' },
      { value: 'button', label: 'classic with buttons' },
      { value: 'wide', label: 'wide' },
      { value: 'wide_button', label: 'wide with buttons' },
      { value: 'wide_tags', label: 'wide with tags' },
      { value: 'slider_left', label: 'slider left' },
      { value: 'slider_left_tags', label: 'slider left with tags' },
      { value: 'slider_right', label: 'slider right' },
      { value: 'slider_right_tags', label: 'slider right with tags' },
      { value: 'plain', label: 'plain (checkboxes)' },
    ];

    if (this.state.selectedInclude && this.state.selectedInclude.length) {
      this.state.groups.forEach((group) => {
        if (this.state.selectedInclude.indexOf(group.term_group) > -1) {
          optionsOneOnlyGroups.push({
            value: group.term_group,
            label: group.label,
          });
        }
      });
    } else if (this.state.groups && this.state.groups.length) {
      this.state.groups.forEach((group) => {
        optionsOneOnlyGroups.push({
          value: group.term_group,
          label: group.label,
        });
      });
    }

    if (this.state.allTaxonomies) {
      for (var key in this.state.allTaxonomies) {
        if (this.state.allTaxonomies.hasOwnProperty(key)) {
          optionsStaticTaxonomy.push({
            value: this.state.allTaxonomies[key].slug,
            label: this.state.allTaxonomies[key].name,
          });
        }
      }
    }

    if (this.state.allTerms) {
      this.state.allTerms.forEach((staticTerm) => {
        if (staticTerm.taxonomy == this.state.selectedStaticTaxonomy) {
          optionsStaticTerms.push({
            value: staticTerm.id,
            label: staticTerm.name,
          });
        }
      });
    }

    let enabledTaxonomiesFlat = [];

    this.state.taxonomies.forEach((taxonomyObject) => {
      enabledTaxonomiesFlat.push(taxonomyObject.slug);
    });

    if (this.state.allTerms) {
      this.state.allTerms.forEach((term) => {
        if (
          enabledTaxonomiesFlat &&
          !enabledTaxonomiesFlat.includes(term.taxonomy)
        ) {
          return;
        }
        optionsExcludeTerms.push({ value: term.id, label: term.name });
        optionsIncludeTerms.push({ value: term.id, label: term.name });
      });
    }

    if (this.state.allTerms) {
      this.state.allTerms.forEach((term) => {
        if (
          this.state.selectedIncludeTerms &&
          this.state.selectedIncludeTerms.length &&
          !this.state.selectedIncludeTerms.includes(term.id)
        ) {
          return;
        }
        if (
          this.state.selectedExcludeTerms &&
          this.state.selectedExcludeTerms.length &&
          this.state.selectedExcludeTerms.includes(term.id)
        ) {
          return;
        }
        optionsPresetTags.push({
          value: term.slug,
          label: term.name,
        });
      });
    }

    const icons = [
      { classEditor: 'no-alt', classFrontend: '' },
      { classEditor: 'tag', classFrontend: 'dashicons-tag' },
      { classEditor: 'yes', classFrontend: 'dashicons-yes' },
      { classEditor: 'yes-alt', classFrontend: 'dashicons-yes-alt' },
      { classEditor: 'heart', classFrontend: 'dashicons-heart' },
      { classEditor: 'arrow-left', classFrontend: 'dashicons-arrow-left' },
      { classEditor: 'arrow-right', classFrontend: 'dashicons-arrow-right' },
      { classEditor: 'thumbs-up', classFrontend: 'dashicons-thumbs-up' },
    ];

    const iconButtons = icons.map((icon, index) => {
      return (
        <Button
          isPressed={icon_class === icon.classFrontend}
          onClick={() => setAttributes({ icon_class: icon.classFrontend })}
          isTertiary={true}
          key={`tgIcon${index}`}
        >
          <Dashicon icon={icon.classEditor} />
        </Button>
      );
    });

    if (source !== 'gutenberg') {
      setAttributes({ source: 'gutenberg' });
    }

    let renderAttributes = { ...attributes };
    delete renderAttributes.cover;
    delete renderAttributes.operator;
    delete renderAttributes.one_only_groupss;
    delete renderAttributes.timeout;
    renderAttributes.source = 'editorPreview';
    const blockType = this.props.name ? getBlockType(this.props.name) : null;
    const blockTitle =
      blockType && blockType.title
        ? blockType.title
        : defaultTPFMenuBlockTitle;
    const renderBlockName = this.props.name || legacyTPFMenuBlockName;

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
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='include_terms'
            />
            <label htmlFor='tg_input_include_terms'>{__('Include tags')}</label>
            <Select
              id='tg_input_include_terms'
              onChange={(options) => handleChangeIncludeTerms(this, options)}
              value={this.state.selectedIncludeTerms}
              options={optionsIncludeTerms}
              multi={true}
              closeOnSelect={false}
              removeSelected={true}
            />
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='exclude_terms'
            />
            <label htmlFor='tg_input_exclude_terms'>{__('Exclude tags')}</label>
            <Select
              id='tg_input_exclude_terms'
              onChange={(options) => handleChangeExcludeTerms(this, options)}
              value={this.state.selectedExcludeTerms}
              options={optionsExcludeTerms}
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
                { value: 'IN', label: __('any tag matches') },
                { value: 'AND', label: __('all tags match') },
                {
                  value: 'IN AND',
                  label: __('at least one tag matches from each group'),
                },
                {
                  value: 'EXACT',
                  label: __('exact match of all selected tags'),
                },
              ]}
            />
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='term_orderby'
            />
            <label htmlFor='tg_input_term_orderby'>{__('Order tags by')}</label>
            <Select
              id='tg_input_term_orderby'
              onChange={(option) => {
                if (option) setAttributes({ term_orderby: option.value });
              }}
              value={
                term_orderby && typeof term_orderby === 'string'
                  ? term_orderby
                  : 'name'
              }
              options={optionsOrderby}
            />
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='term_order'
            />
            <label htmlFor='tg_input_term_order'>
              {__('Sort order of tags')}
            </label>
            <Select
              id='tg_input_term_order'
              onChange={(option) => {
                if (option) setAttributes({ term_order: option.value });
              }}
              value={
                term_order && typeof term_order === 'string'
                  ? term_order.toUpperCase()
                  : 'DESC'
              }
              options={optionsOrder}
            />
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='hide_empty'
            />
            <ToggleControl
              label={__('Hide unused tags.')}
              checked={hide_empty}
              onChange={() => toggleOptionHideEmpty(this)}
            />
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='preset_tags'
            />
            <label htmlFor='tg_input_preset_tags'>{__('Preset tags')}</label>
            <Select
              id='tg_input_preset_tags'
              onChange={(options) => handleChangePresetTags(this, options)}
              value={this.state.selectedPresetTags}
              options={optionsPresetTags}
              multi={true}
              closeOnSelect={false}
              removeSelected={true}
            />
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='static_taxonomy'
            />
            <label htmlFor='tg_input_static_taxonomy'>
              {__('Static taxonomy')}
            </label>
            <Select
              id='tg_input_static_taxonomy'
              onChange={(option) => handleChangeStaticTaxonomy(this, option)}
              value={this.state.selectedStaticTaxonomy}
              options={optionsStaticTaxonomy}
              multi={false}
              closeOnSelect={true}
            />
            {this.state.selectedStaticTaxonomy && (
              <div>
                <TagGroupsHelp
                  url={helpUrl}
                  product={helpProduct}
                  feature={helpFeature}
                  siteLang={siteLang}
                  topic='static_terms'
                />
                <label htmlFor='tg_input_static_terms'>
                  {__('Static terms')}
                </label>
                <Select
                  id='tg_input_static_terms'
                  onChange={(options) => handleChangeStaticTerms(this, options)}
                  value={this.state.selectedStaticTerms}
                  options={optionsStaticTerms}
                  multi={true}
                  closeOnSelect={false}
                  removeSelected={true}
                />
              </div>
            )}
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
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='one_only_groups'
            />
            <label htmlFor='tg_input_one_only_groups'>
              {__('Groups where only one tag can be selected')}
            </label>
            <Select
              id='tg_input_one_only_groups'
              onChange={(options) => handleChangeOneOnlyGroups(this, options)}
              value={this.state.selectedOneOnlyGroups}
              options={optionsOneOnlyGroups}
              multi={true}
              closeOnSelect={false}
              removeSelected={true}
            />
          </PanelBody>

          <PanelBody title={__('Text')} initialOpen={false}>
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='text_search'
            />
            <label htmlFor='tg_input_text_search'>{__('Text Search')}</label>
            <Select
              id='tg_input_text_search'
              onChange={(selected) =>
                setAttributes({ text_search: selected.value })
              }
              value={text_search}
              options={optionsTextSearch}
              multi={false}
              closeOnSelect={true}
            />
          </PanelBody>

          <PanelBody title={__('Caching and Performance')} initialOpen={false}>
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='caching_time'
            />
            <RangeControl
              label={__('Server caching time (minutes)')}
              value={
                typeof caching_time !== 'undefined' ? Number(caching_time) : 10
              }
              onChange={(caching_time) => setAttributes({ caching_time })}
              min={0}
              max={600}
            />
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='persistent_filter'
            />
            <RangeControl
              label={__('Remember selected filter and post (minutes)')}
              value={
                typeof persistent_filter !== 'undefined'
                  ? Number(persistent_filter)
                  : 0
              }
              onChange={(persistent_filter) =>
                setAttributes({ persistent_filter })
              }
              min={0}
              max={600}
            />
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='timeout'
            />
            <RangeControl
              label={__('Timeout before search')}
              value={typeof timeout !== 'undefined' ? Number(timeout) : 1000}
              onChange={(timeout) => setAttributes({ timeout })}
              min={0}
              max={10000}
              step={500}
            />
          </PanelBody>

          {!!text_search && (
            <PanelBody title={__('Labels and Messages')} initialOpen={false}>
              <TagGroupsHelp
                url={helpUrl}
                product={helpProduct}
                feature={helpFeature}
                siteLang={siteLang}
                topic='title_text_search'
              />
              <label htmlFor='tg_input_title_text_search'>
                {__('Title for text search')}
              </label>
              <PlainText
                id='tg_input_title_text_search'
                className='input-control'
                value={title_text_search ? title_text_search : ''}
                placeholder={__('Write here or leave empty.')}
                onChange={(title_text_search) =>
                  setAttributes({ title_text_search })
                }
              />
              <TagGroupsHelp
                url={helpUrl}
                product={helpProduct}
                feature={helpFeature}
                siteLang={siteLang}
                topic='placeholder_text_search'
              />
              <label htmlFor='tg_input_placeholder_text_search'>
                {__('Placeholder for search field')}
              </label>
              <PlainText
                id='tg_input_placeholder_text_search'
                className='input-control'
                value={placeholder_text_search ? placeholder_text_search : ''}
                placeholder={__('Write here or leave empty.')}
                onChange={(placeholder_text_search) =>
                  setAttributes({ placeholder_text_search })
                }
              />
            </PanelBody>
          )}

          <PanelBody title={__('Layout and Theme')} initialOpen={false}>
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='layout'
            />
            <label htmlFor='tg_input_layout'>{__('Menu Layout')}</label>
            <Select
              id='tg_input_layout'
              onChange={(selected) => setAttributes({ layout: selected.value })}
              value={layout}
              options={optionsLayout}
              multi={false}
              closeOnSelect={true}
            />
            {('slider_left' === layout ||
              'slider_right' === layout ||
              'slider_left_tags' === layout ||
              'slider_right_tags' === layout) && (
              <div
                className='tg_space_below'
                dangerouslySetInnerHTML={{
                  __html: __(
                    'To open the sliders you will need the <b>Toggle Post Filter - Slider Button</b> block.'
                  ),
                }}
              ></div>
            )}
            {('button' === layout ||
              'wide_button' === layout ||
              'slider_left' === layout ||
              'slider_right' === layout) && (
              <div className='tg_space_below'>
                <div>
                  <TagGroupsHelp
                    url={helpUrl}
                    product={helpProduct}
                    feature={helpFeature}
                    siteLang={siteLang}
                    topic='icon_class'
                  />
                  <label htmlFor='tg_input_icon_class'>{__('Icon')}</label>
                </div>
                <ButtonGroup>{iconButtons}</ButtonGroup>
              </div>
            )}
            {layout.substring(layout.length - 5) === '_tags' && (
              <div>
                <PanelBody
                  title={__('Color of unselected tags')}
                  initialOpen={false}
                >
                  <ColorPicker
                    id='tg_input_tag_color'
                    color={tag_color}
                    onChangeComplete={(value) =>
                      setAttributes({ tag_color: value.hex })
                    }
                    disableAlpha
                  />
                </PanelBody>
                <PanelBody
                  title={__('Color of selected tags')}
                  initialOpen={false}
                >
                  <ColorPicker
                    id='tg_input_selected_tag_color'
                    color={selected_tag_color}
                    onChangeComplete={(value) =>
                      setAttributes({ selected_tag_color: value.hex })
                    }
                    disableAlpha
                  />
                </PanelBody>
              </div>
            )}
            {(layout === 'slider_left_tags' ||
              layout === 'slider_right_tags') && (
              <Fragment>
                <TagGroupsHelp
                  url={helpUrl}
                  product={helpProduct}
                  feature={helpFeature}
                  siteLang={siteLang}
                  topic='slider_width'
                />
                <RangeControl
                  label={__('Slider width (px)')}
                  value={
                    typeof slider_width !== 'undefined'
                      ? Number(slider_width)
                      : 600
                  }
                  onChange={(slider_width) => setAttributes({ slider_width })}
                  min={200}
                  max={2000}
                />
              </Fragment>
            )}
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
              topic='accordion'
            />
            <label htmlFor='tg_input_accordion'>{__('Hide toggles in an accordion.')}</label>
            <Select
            id='tg_input_accordion'
            onChange={(selected) => setAttributes({ accordion: selected.value })}
            value={accordion}
            options={optionsAccordion}
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
                topic='div_id'
              />
              <label htmlFor='tg_input_div_id'>{'div id'}</label>
            </div>
            <PlainText
              id='div_id'
              className='input-control'
              value={div_id ? div_id : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(div_id) => setAttributes({ div_id })}
            />
          </PanelBody>
          {typeof tagGroupsTPFBodyLoaded !== 'undefined' &&
            !tagGroupsTPFBodyLoaded && (
              <div className='chatty-mango-help-transform chatty-mango-alert'>
                <div
                  dangerouslySetInnerHTML={{
                    __html: __(
                      'For this block you will also need the <b>Toggle Post Filter - Posts</b> block.'
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
        {!cover &&
          serverSideRender &&
          layout !== 'slider_left' &&
          layout !== 'slider_right' &&
          layout !== 'slider_left_tags' &&
          layout !== 'slider_right_tags' && (
            <div>
              <TagGroupsServerSideRender
                block={renderBlockName}
                className='chatty-mango-not-active'
                attributes={renderAttributes}
                onFetched={this.finalizeLayout}
              />
              <div style={{ clear: 'both' }}></div>
            </div>
          )}
        {!cover &&
          !(
            serverSideRender &&
            layout !== 'slider_left' &&
            layout !== 'slider_right' &&
            layout !== 'slider_left_tags' &&
            layout !== 'slider_right_tags'
          ) && (
            <div className='chatty-mango-editor'>
              <div className='chatty-mango-editor-block'>
              </div>
              <div className='chatty-mango-editor-block'>
                <h3 className='chatty-mango-editor-title'>
                  {blockTitle}
                </h3>
                <div className='cm-gutenberg dashicons-before dashicons-admin-generic'>
                  {__(
                    'Select this block and customize the filter in the Inspector.'
                  )}
                </div>
                <div className='cm-gutenberg dashicons-before dashicons-welcome-view-site'>
                  {__('See the output with Preview.')}
                </div>
                {(layout === 'slider_left' || layout === 'slider_right') && (
                  <div className='cm-gutenberg dashicons-before dashicons-warning'>
                    {__('This menu will open in a slider.')}
                  </div>
                )}
                <div className='cm-gutenberg dashicons-before dashicons-warning'>
                  {__(
                    'Please also add the Posts block and optionally the Message Field block.'
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
const tpfMenuAttributes = {
  cover: {
    type: 'string',
    default: '',
  },
  source: {
    // internal indicator to identify Gutebergb blocks
    type: 'string',
    default: '',
  },
  accordion: {
    // configurable in block
    type: 'integer',
    default: 0,
  },
  caching_time: {
    // configurable in block
    type: 'integer',
    default: 10,
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
  exclude: {
    // configurable in block
    type: 'string',
    default: '',
  },
  exclude_terms: {
    // configurable in block
    type: 'string',
    default: '',
  },
  hide_empty: {
    // configurable in block
    type: 'integer',
    default: 1,
  },
  icon_class: {
    // configurable in block
    type: 'string',
    default: '',
  },
  include: {
    // configurable in block
    type: 'string',
    default: '',
  },
  include_terms: {
    // configurable in block
    type: 'string',
    default: '',
  },
  layout: {
    // configurable in block
    type: 'string',
    default: 'classic',
  },
  legacy: {
    // configurable in block
    type: 'integer',
    default: 0,
  },
  one_only_groups: {
    // configurable in block
    type: 'string',
    default: '',
  },
  operator: {
    // configurable in block
    type: 'string',
    default: 'IN',
  },
  persistent_filter: {
    // configurable in block
    type: 'integer',
    default: 30,
  },
  placeholder_text_search: {
    // only in shortcode
    type: 'string',
    default: __('type here'),
  },
  preset_tags: {
    type: 'string',
    default: '',
  },
  selected_tag_color: {
    // configurable in block
    type: 'string',
    default: '#e05500',
  },
  slider_width: {
    // configurable in block
    type: 'integer',
    default: 600,
  },
  static_taxonomy: {
    // configurable in block
    type: 'string',
    default: '',
  },
  static_terms: {
    // configurable in block
    type: 'string',
    default: '',
  },
  tag_color: {
    // configurable in block
    type: 'string',
    default: '#ddd',
  },
  taxonomy: {
    // configurable in block
    type: 'string',
    default: '',
  },
  term_order: {
    // configurable in block
    type: 'string',
    default: '',
  },
  term_orderby: {
    // configurable in block
    type: 'string',
    default: 'name',
  },
  text_search: {
    // configurable in block
    type: 'integer',
    default: 0,
  },
  theme: {
    // configurable in block
    type: 'string',
    default: 'light',
  },
  timeout: {
    // configurable in block
    type: 'integer',
    default: 1000,
  },
  title_text_search: {
    // only in shortcode
    type: 'string',
    default: __('Text Search'),
  },
};

const tpfMenuShortcodeDefaults = {};

Object.keys(tpfMenuAttributes).forEach((attribute) => {
  tpfMenuShortcodeDefaults[attribute] = tpfMenuAttributes[attribute].default;
});

const transformTPFMenuBlockToShortcode = function (attributes) {
  let parameters = [];
  for (var attribute in attributes) {
    if (attributes.hasOwnProperty(attribute)) {
      if (
        null !== attributes[attribute] &&
        '' !== attributes[attribute] &&
        'source' !== attribute &&
        Object.prototype.hasOwnProperty.call(tpfMenuShortcodeDefaults, attribute) &&
        attributes[attribute] !== tpfMenuShortcodeDefaults[attribute]
      ) {
        if (typeof attributes[attribute] === 'number') {
          parameters.push(attribute + '=' + attributes[attribute]);
        } else if (typeof attributes[attribute] === 'boolean') {
          parameters.push(attribute + '=' + (attributes[attribute] ? '1' : '0'));
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

  let text = '[tag_groups_tpf_menu ' + parameters.join(' ') + ']';
  return createBlock('core/shortcode', {
    text,
  });
};

const createTPFMenuBlockSettings = ({ title, layout, inserter = true }) => {
  const attributes = {
    ...tpfMenuAttributes,
    layout: {
      ...tpfMenuAttributes.layout,
      default: layout,
    },
  };

  return {
    title,
    icon: 'filter',
    category: 'chatty-mango',
    description: __(
      'Show a list of posts that corresponds to tags entered by your visitors.'
    ),
    keywords: [__('post filter'), __('menu'), 'Chatty Mango'],
    transforms: {
      to: [
        {
          type: 'block',
          blocks: ['core/shortcode'],
          transform: transformTPFMenuBlockToShortcode,
        },
      ],
    },
    supports: {
      html: false,
      customClassName: false,
      inserter,
      multiple: false,
    },
    example: {
      attributes: {
        cover: 'dynamic-post-filter-toggles.png',
      },
    },
    attributes,
    edit: editFunction,
    save: function () {
      return null;
    },
  };
};

registerBlockType(
  legacyTPFMenuBlockName,
  createTPFMenuBlockSettings({
    title: defaultTPFMenuBlockTitle,
    layout: 'classic',
    inserter: false,
  })
);

[
  {
    name: 'chatty-mango/chatty-mango-tpf-menu-horizontal',
    title: __('Toggle Post Filter - Menu'),
    layout: 'wide',
  },
  {
    name: 'chatty-mango/chatty-mango-tpf-menu-vertical',
    title: __('Toggle Post Filter - Vertical Menu'),
    layout: 'plain',
  },
  {
    name: 'chatty-mango/chatty-mango-tpf-menu-buttons',
    title: __('Toggle Post Filter - Buttons'),
    layout: 'wide_button',
  },
  {
    name: 'chatty-mango/chatty-mango-tpf-menu-vertical-buttons',
    title: __('Toggle Post Filter - Vertical Buttons'),
    layout: 'button',
  },
  {
    name: 'chatty-mango/chatty-mango-tpf-menu-vertical-toggles',
    title: __('Toggle Post Filter - Vertical Toggles'),
    layout: 'classic',
  },
  {
    name: 'chatty-mango/chatty-mango-tpf-menu-slider',
    title: __('Toggle Post Filter - Slider Menu'),
    layout: 'slider_left',
  },
  {
    name: 'chatty-mango/chatty-mango-tpf-menu-slider-buttons',
    title: __('Toggle Post Filter - Slider With Buttons'),
    layout: 'slider_left_tags',
  },
].forEach((variant) => {
  registerBlockType(
    variant.name,
    createTPFMenuBlockSettings({
      title: variant.title,
      layout: variant.layout,
    })
  );
});
