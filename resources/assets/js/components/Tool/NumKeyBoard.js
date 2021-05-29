import React from 'react';

export class NumKeyBoard extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			inputValue: "",
			label: '',
			type: 'number',
			maxlength: 6,
			precision: 0,
		};
		this.handleNumBtnClick = this.handleNumBtnClick.bind(this);
		this.handleBsBtnClick = this.handleBsBtnClick.bind(this);
	}

	handleNumBtnClick(e) {
		// TODO validate
		if (this.state.inputValue.length < this.state.maxlength) {
			let typeNum = e.target.firstChild.data;
			let newValue = this.state.inputValue + typeNum;
			this.setState({inputValue: newValue});
		}
	}

	handleBsBtnClick() {
		if (this.state.inputValue) {
			let newValue = this.state.inputValue.substr(0, this.state.inputValue.length - 1);
			this.setState({inputValue: newValue});
		}
	}

	render() {
		return (
			<div id="numKeyBoard-box">
				<div className="numPadLayerBox">
					<div className="numPadLayerBox-cot">
						<div className="fTop">
							{this.props.title}
						</div>
						<table border="0" cellSpacing="0" cellPadding="0" className="numKeyBoardTable">
							<tr className="inputKeyBoard">
								{this.state.inputValue}
							</tr>
							<tr>
								<td className="numTd" onClick={this.handleNumBtnClick}>7</td>
								<td className="numTd" onClick={this.handleNumBtnClick}>8</td>
								<td className="numTd" onClick={this.handleNumBtnClick}>9</td>
							</tr>
							<tr>
								<td className="numTd" onClick={this.handleNumBtnClick}>4</td>
								<td className="numTd" onClick={this.handleNumBtnClick}>5</td>
								<td className="numTd" onClick={this.handleNumBtnClick}>6</td>
							</tr>
							<tr>
								<td className="numTd" onClick={this.handleNumBtnClick}>1</td>
								<td className="numTd" onClick={this.handleNumBtnClick}>2</td>
								<td className="numTd" onClick={this.handleNumBtnClick}>3</td>
							</tr>
							<tr>
								<td className="bsBtn" onClick={this.handleBsBtnClick}>BS</td>
								<td className="numTd" onClick={this.handleNumBtnClick}>0</td>
								<td className="saveBtn">Save</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		)
	}
}