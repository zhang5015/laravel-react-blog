import React, { Component } from 'react';
import { Breadcrumb, Icon, Spin, message} from 'antd';
import { Link } from 'react-router-dom';
import { MovieForm } from './MovieForm';

export class MovieUpdate extends React.Component {
  constructor(props) {
    super();
    this.state = {
      //文章相关
      id:props.match.params.id,
      movie:{},
      loading:true,
      //标签
      tagsArr:[],
    };
  }
  componentDidMount(props) {
    //获取文章数据
    axios.get(window.apiURL + 'movies/' + this.state.id)
    .then((response) => {
      this.setState({
        movie:response.data.movie,
        loading:false,
        tagsArr:response.data.tags_arr,
      })
    })
    .catch((error) => {
      console.log(error);
    });
  }
  handleSubmit(movie) {
    if (movie.title == '') {
      message.error('标题不能为空');
    }else {
      //更新文章
      axios.post(window.apiURL + 'movies', movie)
      .then((response) => {
        console.log(response);
        if (response.status == 200) {
          message.success(response.data.message)
        }
      })
      .catch((error) => {
        console.log(error);
      });
    }
  }
  render(){
    return (
      <div style={{padding:20}}>
        <Breadcrumb style={{ marginBottom:20 }}>
          <Breadcrumb.Item>
            <Link to="/movies">
            <Icon type="home" />
            <span> 文章管理</span>
            </Link>
          </Breadcrumb.Item>
          <Breadcrumb.Item>
            文章编辑
          </Breadcrumb.Item>
        </Breadcrumb>
        <Spin spinning={this.state.loading}>
          <MovieForm movie={this.state.movie} tagsArr={this.state.tagsArr} isMarkdown={this.state.movie.is_markdown} handleSubmit={this.handleSubmit.bind(this)}/>
        </Spin>
      </div>
    )
  }
}
