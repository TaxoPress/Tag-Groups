export function handleChangeInclude(_this, options) {
  let selectedInclude = options.map(function (option) {
    if (!isNaN(option.value)) {
      return option.value;
    }
  });

  // Set the state
  _this.setState({ selectedInclude });

  // Set the attributes
  _this.props.setAttributes({
    include: selectedInclude.join(','),
  });


  if (_this.state.selectedExclude !== undefined) {
    // remove elements in selectedInclude from selectedExclude
    let selectedExcludeTmp = _this.state.selectedExclude;
    selectedInclude.forEach(function (id) {
      const i = selectedExcludeTmp.indexOf(id);
      if (i > -1) {
        selectedExcludeTmp.splice(i, 1);
      }
    });
    _this.setState({ selectedExclude: selectedExcludeTmp });
    _this.props.setAttributes({
      exclude: selectedExcludeTmp.join(','),
    });
  }

  if (_this.props.show_not_assigned !== undefined) {
    if (selectedInclude.indexOf(0) > -1) {
      _this.props.setAttributes({
        show_not_assigned: 1,
      });
    } else {
      _this.props.setAttributes({
        show_not_assigned: 0,
      });
    }
  }
}

export function handleChangeExclude(_this, options) {
  let selectedExclude = options.map(function (option) {
    if (!isNaN(option.value)) {
      return option.value;
    }
  });

  // Set the state
  _this.setState({ selectedExclude });

  // Set the attributes
  _this.props.setAttributes({
    exclude: selectedExclude.join(','),
  });

  if (_this.state.selectedInclude !== undefined) {
    // remove elements in selectedExclude from selectedInclude
    let selectedIncludeTmp = _this.state.selectedInclude;
    selectedExclude.forEach(function (id) {
      const i = selectedIncludeTmp.indexOf(id);
      if (i > -1) {
        selectedIncludeTmp.splice(i, 1);
      }
    });
    _this.setState({ selectedInclude: selectedIncludeTmp });
    _this.props.setAttributes({
      include: selectedIncludeTmp.join(','),
    });
  }
}

export function handleChangeTaxonomy(_this, options) {
  let selectedTaxonomies = options.map(function (option) {
    if (typeof option.value === 'string') {
      return option.value;
    }
  });

  // Set the state
  _this.setState({ selectedTaxonomies });

  // Set the attributes
  _this.props.setAttributes({
    taxonomy: selectedTaxonomies.join(','),
  });
}

export function toggleOptionActive(_this) {
  let active = _this.props.attributes.active < 0 ? 0 : -1; // -1 is a replacement for false, since data type is integer and 0 is reserved
  _this.props.setAttributes({ active });
}

export function toggleOptionCollapsible(_this) {
  let collapsible = _this.props.attributes.collapsible ? 0 : 1;
  _this.props.setAttributes({ collapsible });
}

export function toggleOptionMouseover(_this) {
  let mouseover = _this.props.attributes.mouseover ? 0 : 1;
  _this.props.setAttributes({ mouseover });
}

export function toggleOptionAdjustSeparatorSize(_this) {
  let adjust_separator_size = _this.props.attributes.adjust_separator_size
    ? 0
    : 1;
  _this.props.setAttributes({ adjust_separator_size });
}

export function toggleOptionAddPremiumFilter(_this) {
  let add_premium_filter = _this.props.attributes.add_premium_filter ? 0 : 1;
  _this.props.setAttributes({ add_premium_filter });
}

export function toggleOptionHideEmptyTabs(_this) {
  let hide_empty_tabs = _this.props.attributes.hide_empty_tabs ? 0 : 1;
  _this.props.setAttributes({ hide_empty_tabs });
}

export function toggleOptionShowTabs(_this) {
  let show_tabs = _this.props.attributes.show_tabs ? 0 : 1;
  _this.props.setAttributes({ show_tabs });
}

export function toggleOptionShowTagCount(_this) {
  let show_tag_count = _this.props.attributes.show_tag_count ? 0 : 1;
  _this.props.setAttributes({ show_tag_count });
}

export function toggleOptionDelay(_this) {
  let delay = _this.props.attributes.delay ? 0 : 1;
  _this.props.setAttributes({ delay });
}

export function toggleOptionHideEmptyContent(_this) {
  let hide_empty_content =
    1 === _this.props.attributes.hide_empty_content ? 0 : 1;
  _this.props.setAttributes({ hide_empty_content });
}

export function toggleOptionShowAccordion(_this) {
  let show_accordion = _this.props.attributes.show_accordion ? 0 : 1;
  _this.props.setAttributes({ show_accordion });
}

export function toggleOptionKeepTogether(_this) {
  let keep_together = _this.props.attributes.keep_together ? 0 : 1;
  _this.props.setAttributes({ keep_together });
}
