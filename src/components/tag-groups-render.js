/**
 * External dependencies
 */
import { isEqual, debounce } from 'lodash';

/**
 * WordPress dependencies
 */
import { Component, RawHTML, Fragment } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';
import { Placeholder, Spinner } from '@wordpress/components';

export function rendererPath(block, attributes = null, urlQueryArgs = {}) {
  return addQueryArgs(`/wp/v2/block-renderer/${block}`, {
    context: 'edit',
    ...(null !== attributes ? { attributes } : {}),
    ...urlQueryArgs,
  });
}

export class TagGroupsServerSideRender extends Component {
  constructor(props) {
    super(props);
    this.state = {
      response: null,
      prevResponse: null,
    };
  }

  componentDidMount() {
    this.isStillMounted = true;
    this.fetch(this.props);
    // Only debounce once the initial fetch occurs to ensure that the first
    // renders show data as soon as possible.
    this.fetch = debounce(this.fetch, 500);
  }

  componentWillUnmount() {
    this.isStillMounted = false;
  }

  componentDidUpdate(prevProps) {
    if (!isEqual(prevProps.attributes, this.props.attributes)) {
      this.fetch(this.props);
    }
  }

  fetch(props) {
    if (!this.isStillMounted) {
      return;
    }
    if (null !== this.state.response) {
      this.setState({ response: null, prevResponse: this.state.response });
    }
    const { block, attributes = null, urlQueryArgs = {}, onFetched } = props;

    const path = rendererPath(block, attributes, urlQueryArgs);
    // Store the latest fetch request so that when we process it, we can
    // check if it is the current request, to avoid race conditions on slow networks.
    const fetchRequest = (this.currentFetchRequest = apiFetch({ path })
      .then((response) => {
        if (
          this.isStillMounted &&
          fetchRequest === this.currentFetchRequest &&
          response
        ) {
          this.setState({ response: response.rendered });
          if (props['onFetched']) {
            props['onFetched']();
          }
        }
      })
      .catch((error) => {
        if (this.isStillMounted && fetchRequest === this.currentFetchRequest) {
          this.setState({
            response: {
              error: true,
              errorMsg: error.message,
            },
          });
        }
      }));
    return fetchRequest;
  }

  render() {
    // inspiration from https://github.com/dgwyer/server-side-render-x/blob/master/server-side-render-x.js
    const { right, top, unit } = this.props.spinnerLocation;
    const response = this.state.response;
    const prevResponse = this.state.prevResponse;

    const {
      className,
      EmptyResponsePlaceholder,
      ErrorResponsePlaceholder,
      LoadingResponsePlaceholder,
    } = this.props;

    if (response === '') {
      return <EmptyResponsePlaceholder response={response} {...this.props} />;
    } else if (!response) {
      return (
        <LoadingResponsePlaceholder
          right={right}
          top={top}
          unit={unit}
          prevResponse={prevResponse}
        />
      );
    } else if (response.error) {
      return <ErrorResponsePlaceholder response={response} {...this.props} />;
    }

    return (
      <RawHTML key='html' className={className}>
        {response}
      </RawHTML>
    );
  }
}

TagGroupsServerSideRender.defaultProps = {
  spinnerLocation: { right: 0, top: 10, unit: 'px' },
  EmptyResponsePlaceholder: ({ className }) => (
    <Placeholder className={className}>
      {__('Block rendered as empty.')}
    </Placeholder>
  ),
  ErrorResponsePlaceholder: ({ response, className }) => {
    const errorMessage = sprintf(
      // translators: %s: error message describing the problem
      __('Error loading block: %s'),
      response.errorMsg
    );
    return <Placeholder className={className}>{errorMessage}</Placeholder>;
  },
  LoadingResponsePlaceholder: ({
    className,
    top,
    right,
    unit,
    prevResponse,
  }) => {
    return (
      <Fragment>
        <div style={{ position: 'relative', minHeight: '100px' }}>
          <div
            style={{
              position: 'absolute',
              right: `${right}${unit}`,
              top: `${top}${unit}`,
            }}
          >
            <Spinner />
          </div>
          <RawHTML key='html' className={className}>
            {prevResponse}
          </RawHTML>
        </div>
      </Fragment>
    );
  },
};

export default TagGroupsServerSideRender;
