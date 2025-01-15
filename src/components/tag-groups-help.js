import React from 'react';
const { __ } = wp.i18n;

const tagGroupsHelp = (props) => {
  let href;

  if (props.topic === 'transform-your-block-for-more-options') {
    href =
      props.url +
      props.product +
      '/front-end-features/how-to-use-gutenberg-blocks/#transforming-blocks-to-shortcodes';
  } else {
    href = props.url + props.product + '/' + props.feature;

    if ('' != props.siteLang) {
      href += '?lang=' + props.siteLang;
    }

    href += '#' + props.topic;
  }

  if (props.url || props.url !== '' || href || href !== '') {
    return '';
  }

  return (
    <div>
      <a
        href={href}
        target='_blank'
        style={{ textDecoration: 'none' }}
        title={__('Click for help!')}
      >
        <span className='dashicons dashicons-editor-help tg_right chatty-mango-help-icon'></span>
      </a>
    </div>
  );
};

export default tagGroupsHelp;
