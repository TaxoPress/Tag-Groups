/**
 * BLOCK: chatty-mango-guten-dpfwt-body
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

import {
  encodeTemplate,
  decodeTemplate,
  optionsOrderbyPosts,
  optionsOrder,
  optionsTheme,
} from '../modules/functions';

import {
  TagGroupsIcon,
  HorizontalRuler,
  ClearFloats,
} from '../components/general';

import { toggleOptionDefaultShowPosts } from '../modules/handlers';

const { __ } = wp.i18n;

const { createBlock, registerBlockType } = wp.blocks;

const { InspectorControls, PlainText } = wp.editor;

const { PanelBody, ToggleControl, RangeControl } = wp.components;

const { Component, Fragment } = wp.element;

const {
  siteLang,
  pluginUrl,
  hasPremium,
  templates,
  serverSideRender,
} = ChattyMangoTagGroupsGlobal;

const helpUrl = '';
const helpProduct = hasPremium ? 'tag-groups-premium' : 'tag-groups';
const helpFeature =
  'toggle-post-filter/toggle-post-filter-body-gutenberg-block/';

class editFunction extends Component {
  // Method for setting the initial state.
  static getInitialState(attributes) {
    const layout = attributes.layout || 'classic';

    const theme = attributes.theme || 'light';

    return {
      layout,
      theme,
    };
  }

  // Constructing our component. With super() we are setting everything to 'this'.
  // Now we can access the attributes with this.props.attributes
  constructor() {
    super(...arguments);

    const { attributes, setAttributes } = this.props;

    this.state = this.constructor.getInitialState(attributes);

    if (attributes.template) {
      const decodedTemplate = decodeTemplate(attributes.template);
      if (decodedTemplate === attributes.template) {
        setAttributes({ template: encodeTemplate(attributes.template) });
      }
    }
  }

  render() {
    const { attributes, setAttributes } = this.props;

    const {
      cover,
      default_image_src,
      div_class,
      div_id,
      source,
      default_show_posts,
      display_amount,
      layout,
      legacy,
      message_amount_plural,
      message_amount_singular,
      message_nothing_found,
      message_load_more,
      message_go_back,
      order,
      orderby,
      pager,
      pager_position,
      posts_per_page,
      posts_placeholder,
      template,
      theme,
      transition,
    } = attributes;

    const decodedTemplate = decodeTemplate(template);

    let optionsLayout = [
      { value: 'classic', label: 'classic' },
      { value: 'wide', label: 'wide' },
      { value: 'boxed', label: 'boxed' },
      { value: 'masonry', label: 'masonry' },
      { value: 'masonry-small', label: 'masonry (small)' },
      { value: 'masonry-large', label: 'masonry (large)' },
      { value: 'columns', label: 'columns' },
      {
        value: 'columns-keep-together',
        label: 'columns (avoid splitting posts)',
      },
    ];

    if (source !== 'gutenberg') {
      setAttributes({ source: 'gutenberg' });
    }

    if (typeof tagGroupsTPFBodyLoaded !== 'undefined' && !attributes.cover) {
      tagGroupsTPFBodyLoaded = true;
    }

    if (
      typeof tagGroupsToggleFilterMenuLegacy !== 'undefined' &&
      !attributes.cover
    ) {
      setAttributes({ legacy: tagGroupsToggleFilterMenuLegacy });
    }

    const renderAttributes = {
      default_image_src,
      message_go_back,
      message_load_more,
      order,
      orderby,
      pager,
      pager_position,
      posts_per_page,
      template,
    };

    let postListShortened = false;

    if (renderAttributes.posts_per_page > 3) {
      renderAttributes.posts_per_page = 3;
      postListShortened = true;
    }

    return [
      <InspectorControls key='inspector'>
        <div className='chatty-mango-inspector-control'>
          {!legacy && (
            <Fragment>
              <PanelBody title={__('Posts')} initialOpen={false}>
                <TagGroupsHelp
                  url={helpUrl}
                  product={helpProduct}
                  feature={helpFeature}
                  siteLang={siteLang}
                  topic='default_show_posts'
                />
                <ToggleControl
                  label={__('Show all posts when no filter is activated.')}
                  checked={default_show_posts}
                  onChange={() => toggleOptionDefaultShowPosts(this)}
                />
                <TagGroupsHelp
                  url={helpUrl}
                  product={helpProduct}
                  feature={helpFeature}
                  siteLang={siteLang}
                  topic='posts_per_page'
                />
                <RangeControl
                  label={__('Posts per page')}
                  value={
                    typeof posts_per_page !== 'undefined'
                      ? Number(posts_per_page)
                      : 22
                  }
                  onChange={(posts_per_page) =>
                    setAttributes({ posts_per_page })
                  }
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
                    { value: 1, label: __('previous and next') },
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
                        if (option)
                          setAttributes({ pager_position: option.value });
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
                  topic='orderby'
                />
                <label htmlFor='tg_input_orderby'>{__('Order posts by')}</label>
                <Select
                  id='tg_input_orderby'
                  onChange={(option) => {
                    if (option) setAttributes({ orderby: option.value });
                  }}
                  value={
                    orderby && typeof orderby === 'string' ? orderby : 'title'
                  }
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
                <label htmlFor='tg_display_amount'>
                  {__('Display how many posts were found.')}
                </label>
                <Select
                  id='tg_display_amount'
                  onChange={(option) => {
                    if (option) setAttributes({ display_amount: option.value });
                  }}
                  value={display_amount}
                  options={[
                    { value: 0, label: __('off') },
                    { value: 1, label: __('message block') },
                    { value: 2, label: __('overlay notification') },
                  ]}
                />
                <TagGroupsHelp
                  url={helpUrl}
                  product={helpProduct}
                  feature={helpFeature}
                  siteLang={siteLang}
                  topic='transition'
                />
                <label htmlFor='tg_input_transition'>{__('Transition')}</label>
                <Select
                  id='tg_input_transition'
                  onChange={(option) => {
                    if (option) setAttributes({ transition: option.value });
                  }}
                  value={
                    transition && typeof transition === 'string'
                      ? transition
                      : ''
                  }
                  options={[
                    { value: '', label: __('none') },
                    { value: 'fade', label: __('fade') },
                  ]}
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
                <PanelBody
                  title={__('Customize the template')}
                  initialOpen={false}
                >
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
            </Fragment>
          )}

          <PanelBody title={__('Post Layout and Theme')} initialOpen={false}>
            <TagGroupsHelp
              url={helpUrl}
              product={helpProduct}
              feature={helpFeature}
              siteLang={siteLang}
              topic='layout_body'
            />
            <label htmlFor='tg_input_layout'>{__('Post Layout')}</label>
            <Select
              id='tg_input_layout'
              onChange={(selected) => setAttributes({ layout: selected.value })}
              value={layout}
              options={optionsLayout}
              multi={false}
              closeOnSelect={true}
            />
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
            {!!legacy && (
              <div>
                <span
                  className='dashicons dashicons-warning tg_left'
                  style={{ marginRight: 5 }}
                ></span>
                <div
                  dangerouslySetInnerHTML={{
                    __html: __(
                      'Set the post templates in the <b>Toggle Post Filter - Menu</b> block.'
                    ),
                  }}
                ></div>
              </div>
            )}
          </PanelBody>

          {!legacy && (
            <PanelBody title={__('Labels and Messages')} initialOpen={false}>
              <TagGroupsHelp
                url={helpUrl}
                product={helpProduct}
                feature={helpFeature}
                siteLang={siteLang}
                topic='posts_placeholder'
              />
              <label htmlFor='tg_input_posts_placeholder'>
                {__('Placeholder for posts field')}
              </label>
              <PlainText
                id='tg_input_posts_placeholder'
                className='input-control'
                value={posts_placeholder ? posts_placeholder : ''}
                placeholder={__('Write here or leave empty.')}
                onChange={(posts_placeholder) =>
                  setAttributes({ posts_placeholder })
                }
              />
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
              {!!pager && (
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
              {!!display_amount && (
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
                    value={
                      message_amount_singular ? message_amount_singular : ''
                    }
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
          )}

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
              id='tg_input_div_id'
              className='input-control'
              value={div_id ? div_id : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(div_id) => setAttributes({ div_id })}
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
              block='chatty-mango/tag-groups-premium-post-filter' // Yes! We are (mis)using the post filter here
              className='chatty-mango-not-active-all'
              attributes={renderAttributes}
            />
            <div className='chatty-mango-watermark'>{__('sample posts')}</div>
            {postListShortened && (
              <div className='chatty-mango-faded' style={{ border: 'none' }}>
                {__(
                  'We shortened the list of posts. The actual maximum number is:'
                )}{' '}
                {posts_per_page}
              </div>
            )}
          </div>
        )}
        {!cover && !serverSideRender && (
          <div className='chatty-mango-editor'>
            <div className='chatty-mango-editor-block'>
            </div>
            <div className='chatty-mango-editor-block'>
              <h3 className='chatty-mango-editor-title'>
                {__('Toggle Post Filter - Posts')}
              </h3>
              <div className='cm-gutenberg dashicons-before dashicons-admin-generic'>
                {__(
                  'Select this block and customize the filter in the Inspector.'
                )}
              </div>
              <div className='cm-gutenberg dashicons-before dashicons-warning'>
                {__(
                  'Please also add the Menu block and optionally the Message Field block.'
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
var cmTagGroupsDPFBodyBlock = registerBlockType(
  'chatty-mango/chatty-mango-guten-dpfwt-body',
  {
      title: __('Toggle Post Filter - Posts'),
      icon: 'filter', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
      category: 'chatty-mango',
      description: __(
        'Show a list of posts that corresponds to tags entered by your visitors.'
      ),
      keywords: [__('post filter'), __('posts'), __('body'), 'Chatty Mango'],
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
                    cmTagGroupsDPFBodyBlock.attributes[attribute] &&
                    attributes[attribute] !==
                      cmTagGroupsDPFBodyBlock.attributes[attribute].default
                  ) {
                    if (typeof attributes[attribute] === 'number') {
                      parameters.push(attribute + '=' + attributes[attribute]);
                    } else if (typeof attributes[attribute] === 'boolean') {
                      parameters.push(
                        attribute + '=' + (attributes[attribute] ? '1' : '0')
                      );
                    } else {
                      let str = attributes[attribute];
                      // keep encoded template for better compatibility
                      // if ('template' === attribute) {
                      //   try {
                      //     str = decodeURIComponent(atob(str));
                      //   } catch (error) {}
                      //   str = str
                      //     .replace(/(\r\n|\n|\r)/gm, '')
                      //     .replace(/&/g, '&amp;')
                      //     .replace(/</g, '&lt;')
                      //     .replace(/>/g, '&gt;')
                      //     .replace(/"/g, '&quot;');
                      // }
                      if (str.indexOf('"') === -1) {
                        parameters.push(attribute + '="' + str + '"');
                      } else {
                        parameters.push(attribute + "='" + str + "'");
                      }
                    }
                  }
                }
              }

              let text = '[tag_groups_tpf_body ' + parameters.join(' ') + ']';
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
        default_image_src: {
          // configurable in block
          type: 'string',
          default: '',
        },
        default_show_posts: {
          // configurable in block
          type: 'integer',
          default: 0,
        },
        display_amount: {
          // configurable in block
          type: 'integer',
          default: 2,
        },
        div_class: {
          // configurable in block
          type: 'string',
          default: '',
        },
        div_id: {
          // configurable in block
          type: 'string',
          default: 'tag_groups_dpf_toggle_body',
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
        message_go_back: {
          // configurable in block
          type: 'string',
          default: __('Go back'),
        },
        message_load_more: {
          // configurable in block
          type: 'string',
          default: __('Load more'),
        },
        message_nothing_found: {
          // configurable in block
          type: 'string',
          default: __('Nothing found.'),
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
        posts_placeholder: {
          // configurable in block
          type: 'string',
          default: __('Please select a tag.'),
        },
        template: {
          // configurable in block
          type: 'string',
          default: '', // Needs to be empty to apply default template from settings
        },
        theme: {
          // configurable in block
          type: 'string',
          default: 'light',
        },
        transition: {
          // configurable in block
          type: 'string',
          default: 'fade',
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
