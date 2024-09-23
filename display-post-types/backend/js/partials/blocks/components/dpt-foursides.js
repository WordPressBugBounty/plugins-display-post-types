const { Component } = wp.element;
const { __ } = wp.i18n;
const { RangeControl } = wp.components;

class DptFourSides extends Component {
    constructor(props) {
        super(props);
        this.fourSidesContainer = React.createRef();
        this.onChange = this.onChange.bind(this);
    }

    onChange = (index, value) => {
        const { onChange, fourSides, hasDefault } = this.props;
        const defaultVal = hasDefault ? [0, 0, 10, 0] : [0, 0, 0, 0];
        const newFourSides = Array.isArray(fourSides) && fourSides.length !== 0 ? [...fourSides] : [...defaultVal];
        newFourSides[index] = value;
        onChange(newFourSides);
    };

    render() {
        const { fourSides, label, max, hasDefault } = this.props;
        const fourSidesLabel = [
            __('Top', 'display-post-types'),
            __('Right', 'display-post-types'),
            __('Bottom', 'display-post-types'),
            __('Left', 'display-post-types'),
        ];
        const defaultVal = hasDefault ? [0, 0, 10, 0] : [0, 0, 0, 0];
        const newFourSides = Array.isArray(fourSides) && fourSides.length !== 0 ? [...fourSides] : [...defaultVal];

        return (
            <div className="dpt-four-sides-container" ref={this.fourSidesContainer}>
                <h2 className="dpt-four-sides-title">{label}</h2>
                {newFourSides.map((val, index) => (
                    <div
                        className="dpt-four-sides-item"
                        key={index}
                    >
                        <RangeControl
                            label={fourSidesLabel[index]}
                            value={val}
                            onChange={(value) => this.onChange(index, value)}
                            min={0}
                            max={max}
                            step={1}
                        />
                    </div>
                ))}
            </div>
        );
    }
}

export default DptFourSides;
