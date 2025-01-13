const { Component } = wp.element;
const { __ } = wp.i18n;
const { ToggleControl, Icon } = wp.components;

class DptAccordion extends Component {
  constructor(props) {
    super(props);
    this.state = {
      isOpen: !! this.props.initialOpen,
    };
  }

  togglePanel = () => {
    this.setState((prevState) => ({
      isOpen: !prevState.isOpen,
    }));
  };

  render() {
    const { isOpen } = this.state;
    const { title, children, checkVal, checkArray, onItemChange } = this.props;
    const getCheckbox = (key) => {
			return (
				<div>
					<ToggleControl
						checked={ !! checkArray.includes(key) }
						onChange={ () => { onItemChange(key); } }
					/>
				</div>
			);
		};

    const isAccordionEnabled = () => {
      if ( checkArray && ! checkArray.includes(checkVal) ) {
        return false;
      }

      const hasChildren = Array.isArray(children) ? children.length > 0 : React.isValidElement(children);
      if (! hasChildren) {
        return false;
      }

      return true;
    }

    const wrapperClass = isAccordionEnabled() ? 'dpt-accordion' : 'dpt-accordion dpt-accordion-disabled';

    return (
      <div className={wrapperClass} data-key={checkVal}>
        <div className="dpt-accordion__header" onClick={this.togglePanel}>
          <h2 className="dpt-accordion__title">{title}</h2>
          {
            !! checkArray && (
              <div className="dpt-accordion__checkbox" onClick={ (e) => { e.stopPropagation() } }>{getCheckbox(checkVal)}</div>
            )
          }
          {(! isOpen || ! isAccordionEnabled()) && (<span aria-hidden="true" className="dpt-accordion__icon"><Icon icon="arrow-down-alt2"/></span>)}
          { !! isOpen && isAccordionEnabled() && (<span aria-hidden="true" className="dpt-accordion__icon"><Icon icon="arrow-up-alt2"/></span>)}
        </div>
        {isOpen && isAccordionEnabled() && (
          <div className="dpt-accordion__content">
            {children}
          </div>
        )}
      </div>
    );
  }
}

export default DptAccordion;
