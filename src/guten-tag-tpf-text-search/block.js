/**
 * BLOCK: chatty-mango-tpf-text-search
 *
 * @package Tag Groups
 */

import '../editor.css';

import Select from 'react-select';

import TagGroupsHelp from '../components/tag-groups-help';

const { __ } = wp.i18n;

const { createBlock, registerBlockType } = wp.blocks;

const { InspectorControls, PlainText } = wp.editor;

const { Component, Fragment } = wp.element;

const { siteLang, pluginUrl, hasPremium } = ChattyMangoTagGroupsGlobal;

const helpUrl = '';
const helpProduct = hasPremium ? 'tag-groups-premium' : 'tag-groups';
const helpFeature =
  'toggle-post-filter/toggle-post-filter-text-search-gutenberg-block/';

class editFunction extends Component {
  render() {
    const { attributes, setAttributes } = this.props;

    const { cover, placeholder, search_trigger, source } = attributes;

    const optionsSearchTrigger = [
      { value: 1, label: 'on enter' },
      { value: 2, label: 'on enter or timed' },
    ];

    if (source !== 'gutenberg') {
      setAttributes({ source: 'gutenberg' });
    }

    return [
      <InspectorControls key='inspector'>
        <div className='chatty-mango-inspector-control'>
          <TagGroupsHelp
            url={helpUrl}
            product={helpProduct}
            feature={helpFeature}
            siteLang={siteLang}
            topic='search_trigger'
          />
          <label htmlFor='tg_input_search_trigger'>
            {__('Search Trigger')}
          </label>
          <Select
            id='tg_input_search_trigger'
            onChange={(selected) =>
              setAttributes({ search_trigger: selected.value })
            }
            value={search_trigger}
            options={optionsSearchTrigger}
            multi={false}
            closeOnSelect={true}
          />
          <TagGroupsHelp
            url={helpUrl}
            product={helpProduct}
            feature={helpFeature}
            siteLang={siteLang}
            topic='placeholder'
          />
          <label htmlFor='tg_input_placeholder'>
            {__('Placeholder for search field')}
          </label>
          <PlainText
            id='tg_input_placeholder'
            className='input-control'
            value={placeholder ? placeholder : ''}
            placeholder={__('Write here or leave empty.')}
            onChange={(placeholder) => setAttributes({ placeholder })}
          />
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
          <div
            className='chatty-mango-inspector-help dashicons-before dashicons-info'
            dangerouslySetInnerHTML={{
              __html: __(
                'This block requires that text search is turned off in the menu block.'
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
        {!cover && (
          <input
            id='tg_dpf_toggle_text_search'
            className='tg_dpf_toggle_text_search'
            type='text'
            placeholder={placeholder}
          />
        )}
      </div>,
    ];
  }
}

const cmTagGroupsDPFMenuBlock = registerBlockType(
  'chatty-mango/chatty-mango-tpf-text-search',
  {
    title: __('Toggle Post Filter - Text Search'),
    icon: 'filter',
    category: 'chatty-mango',
    description: __(
      'Show a list of posts that corresponds to tags entered by your visitors.'
    ),
    keywords: [__('post filter'), __('text search'), 'Chatty Mango'],
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
                  cmTagGroupsDPFMenuBlock.attributes[attribute] &&
                  attributes[attribute] !==
                    cmTagGroupsDPFMenuBlock.attributes[attribute].default
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

            let text =
              '[tag_groups_tpf_text_search ' + parameters.join(' ') + ']';
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
      multiple: true,
    },
    example: {
      attributes: {
        cover: 'dynamic-post-filter-toggles.png',
      },
    },
    attributes: {
      cover: {
        type: 'string',
        default: '',
      },
      source: {
        type: 'string',
        default: '',
      },
      placeholder: {
        type: 'string',
        default: 'type here',
      },
      search_trigger: {
        type: 'integer',
        default: 2,
      },
    },
    edit: editFunction,
    save: function () {
      return null;
    },
  }
);
